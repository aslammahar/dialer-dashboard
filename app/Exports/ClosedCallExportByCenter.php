<?php

namespace App\Exports;

use App\Models\ClosedCall;
use App\Models\User;
use App\Models\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Log;

class ClosedCallExportByCenter implements FromCollection, WithHeadings, WithMapping
{
    protected $centerName;
    protected $user;
    protected $limit;
    protected $filters;
    protected $isSystemExport;
    protected $recordCount = 0;

    /**
     * Constructor - Accepts center name, user context and filters for authorization
     * 
     * @param string $centerName Center name to filter by
     * @param User|null $user User requesting the export (null for system exports)
     * @param int $limit Maximum number of records to export
     * @param array $filters Additional filters (date range, status, etc.)
     * @param bool $isSystemExport If true, bypasses user authorization (for scheduled backups)
     */
    public function __construct($centerName, $limit = 1000, $filters = [], $isSystemExport = false, $user = null)
    {
        $this->centerName = $centerName;
        $this->user = $user ?? Auth::user();
        $this->limit = $limit;
        $this->filters = $filters;
        $this->isSystemExport = $isSystemExport;
    }

    /**
     * Get the collection of records with authorization checks and center name filter
     */
    public function collection()
    {
        // System exports (like scheduled backups) - require special permission or be run from console
        if ($this->isSystemExport) {
            // Only allow system exports from console commands or users with 'export all data' permission
            if (!app()->runningInConsole()) {
                if (!$this->user) {
                    Log::warning('Unauthorized system export attempt - no user', [
                        'ip' => request()->ip(),
                        'center_name' => $this->centerName
                    ]);
                    return collect([]);
                }
                
                // Check if user has permission using Gate facade
                $hasPermission = Gate::forUser($this->user)->allows('export all data');
                if (!$hasPermission) {
                    Log::warning('Unauthorized system export attempt', [
                        'user_id' => $this->user->id ?? null,
                        'ip' => request()->ip(),
                        'center_name' => $this->centerName
                    ]);
                    return collect([]); // Return empty collection
                }
            }
            
            // System export - filter by center name and return all records till date
            $query = ClosedCall::where('center_name', $this->centerName);
            
            // Apply additional filters if provided
            if (isset($this->filters['start_date']) && isset($this->filters['end_date'])) {
                $query->whereBetween('created_at', [
                    $this->filters['start_date'],
                    $this->filters['end_date']
                ]);
            } elseif (isset($this->filters['start_date'])) {
                $query->where('created_at', '>=', $this->filters['start_date']);
            } elseif (isset($this->filters['end_date'])) {
                $query->where('created_at', '<=', $this->filters['end_date']);
            }

            if (isset($this->filters['status'])) {
                $query->where('status', $this->filters['status']);
            }

            if (isset($this->filters['agent_status'])) {
                $query->where('agent_status', $this->filters['agent_status']);
            }
            
            // Get all records (no limit) - all records till date
            $collection = $query->latest()->get();
            $this->recordCount = $collection->count();
            
            Log::info('ClosedCall export by center executed (system)', [
                'center_name' => $this->centerName,
                'record_count' => $this->recordCount,
                'filters' => $this->filters
            ]);
            
            return $collection;
        }

        // User-based exports - require authentication
        if (!$this->user) {
            Log::warning('Export attempted without authentication', [
                'ip' => request()->ip(),
                'center_name' => $this->centerName
            ]);
            return collect([]);
        }

        // Check if user has permission to export closed call data using Gate facade
        $hasClosedCallPermission = Gate::forUser($this->user)->allows('closed call records');
        $hasExportPermission = Gate::forUser($this->user)->allows('export closed calls');
        
        if (!$hasClosedCallPermission && !$hasExportPermission) {
            Log::warning('Unauthorized export attempt', [
                'user_id' => $this->user->id,
                'user_type' => $this->user->type ?? 'unknown',
                'ip' => request()->ip(),
                'center_name' => $this->centerName
            ]);
            return collect([]);
        }

        // Build query with authorization filters and center name
        $query = ClosedCall::where('center_name', $this->centerName);

        // Apply user-based filtering based on user type
        if ($this->user->type === 'client') {
            // Client users can only export their own records
            $authUserEmail = $this->user->email;
            $client = Client::where('email', $authUserEmail)->first();
            
            if ($client) {
                // PARENT CLIENT LOGIC
                $clientId = $client->id;
                $associatedUserIds = User::where('type', 'client')
                    ->where('client_id', $clientId)
                    ->pluck('id')
                    ->toArray();
                
                $associatedUserIds[] = $this->user->id;
                $associatedUserIds = array_unique($associatedUserIds);
                
                if (!empty($associatedUserIds)) {
                    $query->whereIn('clients_id', $associatedUserIds);
                } else {
                    $query->where('id', 0); // Return no results
                }
            } else {
                // CHILD CLIENT LOGIC
                $query->where('clients_id', $this->user->id);
            }
        } elseif ($this->user->type === 'closer') {
            // Closers can only export their own records
            $query->where('closer_id', $this->user->id);
        }
        // For admin/super admin types, they can export all records (already checked permission above)

        // Apply additional filters if provided
        if (isset($this->filters['start_date']) && isset($this->filters['end_date'])) {
            $query->whereBetween('created_at', [
                $this->filters['start_date'],
                $this->filters['end_date']
            ]);
        } elseif (isset($this->filters['start_date'])) {
            $query->where('created_at', '>=', $this->filters['start_date']);
        } elseif (isset($this->filters['end_date'])) {
            $query->where('created_at', '<=', $this->filters['end_date']);
        }

        if (isset($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        if (isset($this->filters['agent_status'])) {
            $query->where('agent_status', $this->filters['agent_status']);
        }

        // Log the export for audit purposes
        $recordCount = $query->count();
        Log::info('ClosedCall export by center executed', [
            'user_id' => $this->user->id,
            'user_type' => $this->user->type,
            'center_name' => $this->centerName,
            'record_count' => $recordCount,
            'filters' => $this->filters,
            'ip' => request()->ip()
        ]);

        // Get all records (no limit) - all records till date
        $collection = $query->latest()->get();
        $this->recordCount = $collection->count();
        
        return $collection;
    }

    /**
     * Get the record count
     */
    public function getRecordCount()
    {
        return $this->recordCount;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Customer Full Name',
            'Phone Number',
            'Alternate Phone Number',
            'Email',
            'Address',
            'City',
            'State',
            'Zip Code',
            'Gender',
            'Marital Status',
            'Age',
            'DOB',
            'Place of Birth',
            'Height',
            'Weight',
            'Social Security',
            'Smoker',
            'Health Condition',
            'Medication',
            'Hospital Name',
            'Hospital Address',
            'Physician Name',
            'Monthly Premium',
            'Carrier',
            'Coverage Plan',
            'Customer Eligibility',
            'Beneficiary',
            'Beneficiary Relation',
            'Beneficiary Phone',
            'Beneficiary DOB',
            'Payor',
            'Bank Name',
            'Bank Address',
            'Routing Number',
            'Bank Account Number',
            'Debit Card No',
            'Debit Expiration',
            'Debit CVV',
            'Account Type',
            'Initial Draft Date',
            'Future Draft Date',
            'Underwriter Name',
            'Remarks',
            'Closer ID',
            'Junior Closer Name',
            'Center Name',
            'Sale Made By',
            'Status',
            'Client\'s Comment',
            'Client ID',
            'Closer Name',
            'Junior Closer 2',
            'Lead ID',
            'Team Name',
            'Agent Name',
            'Dialer Agent Name',
            'Dialer Name',
            'List ID 2',
            'List ID 1',
            'Dialer Agent Name (Duplicate)', // duplicated key warning
            'Recording ID',
            'HIPAA ID',
            'Policy ID',
            'Created At',
            'Updated At',

        ];
    }

    /**
     * Map the row data with sensitive data masking based on permissions
     */
    public function map($row): array
    {
        // Check if user has permission to view sensitive data
        $canViewSensitiveData = false;
        
        if ($this->isSystemExport) {
            $canViewSensitiveData = true; // System exports can view all data
        } elseif ($this->user) {
            // Check permissions using Gate facade
            $hasViewSensitivePermission = Gate::forUser($this->user)->allows('view sensitive data');
            $hasExportAllPermission = Gate::forUser($this->user)->allows('export all data');
            $isAdminType = in_array($this->user->type ?? '', ['super admin', 'admin', 'Director']);
            
            $canViewSensitiveData = $hasViewSensitivePermission || $hasExportAllPermission || $isAdminType;
        }

        // Mask sensitive data if user doesn't have permission
        $maskSensitive = function($value) use ($canViewSensitiveData) {
            if (!$canViewSensitiveData && !empty($value)) {
                return '***REDACTED***';
            }
            return $value;
        };

        // Mask partial data (show last 4 digits for phone/account numbers)
        $maskPartial = function($value, $showLast = 4) use ($canViewSensitiveData) {
            if (!$canViewSensitiveData && !empty($value) && strlen($value) > $showLast) {
                return str_repeat('*', strlen($value) - $showLast) . substr($value, -$showLast);
            }
            return $value;
        };

        return [
            $row->id ?? '',
            $row->customer_full_name,
            $maskPartial($row->phone_number ?? ''),
            $maskPartial($row->alternate_phone_number ?? ''),
            $maskSensitive($row->cx_email ?? ''),
            $maskSensitive($row->address ?? ''),
            $row->city ?? '',
            $row->state ?? '',
            $row->zip_code ?? '',
            $row->gender ?? '',
            $row->martial_status ?? '',
            $row->age ?? '',
            $canViewSensitiveData ? ($row->dob ?? '') : '***REDACTED***',
            $row->palce_of_birth ?? '',
            $row->height ?? '',
            $row->weight ?? '',
            $maskSensitive($row->social_security ?? ''),
            $row->smoker ?? '',
            $maskSensitive($row->health_condition ?? ''),
            $maskSensitive($row->medication ?? ''),
            $row->hospital_name ?? '',
            $maskSensitive($row->hospital_address ?? ''),
            $row->physician_name ?? '',
            $row->monthly_premium ?? '',
            $row->carrier ?? '',
            $row->coverage_plan ?? '',
            $row->customer_eligibility ?? '',
            $row->beneficiary ?? '',
            $row->beneficiary_relation ?? '',
            $maskPartial($row->beneficiary_phone ?? ''),
            $canViewSensitiveData ? ($row->beneficiary_dob ?? '') : '***REDACTED***',
            $row->payor ?? '',
            $row->bank_name ?? '',
            $maskSensitive($row->bank_address ?? ''),
            $maskSensitive($row->routing_number ?? ''),
            $maskSensitive($row->bank_account_number ?? ''),
            $maskSensitive($row->debit_card_direct_express_no ?? ''),
            $maskSensitive($row->debit_card_direct_express_expiration ?? ''),
            $maskSensitive($row->debit_card_direct_express_cvv ?? ''),
            $row->account_type ?? '',
            $row->initial_draft_date ?? '',
            $row->future_draft_date ?? '',
            $row->underwriter_name ?? '',
            $row->remarks ?? '',
            $row->closer_id ?? '',
            $row->junior_closer_name ?? '',
            $row->center_name ?? '',
            $row->sale_made_by ?? '',
            $row->status ?? '',
            $row->clients_comment ?? '',
            $row->clients_id ?? '',
            $row->closername ?? '',
            $row->juniorcloser2 ?? '',
            $row->lead_id ?? '',
            $row->teamname ?? '',
            $row->agentname ?? '',
            $row->dialeragentname ?? '',
            $row->dialername ?? '',
            $row->list_id_2 ?? '',
            $row->list_id_1 ?? '',
            $row->dialeragentname ?? '', // duplicate key
            $row->recording_id ?? '',
            $row->hippa_id ?? '',
            $row->policy_id ?? '',
            optional($row->created_at)->format('Y-m-d H:i:s'),
            optional($row->updated_at)->format('Y-m-d H:i:s'),
        ];
    }
}
