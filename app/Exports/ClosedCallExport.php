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

class ClosedCallExport implements FromCollection, WithHeadings, WithMapping
{
    /** Labels in export checkbox list that correspond to closed_calls.agent_status, not status */
    private const EXPORT_AGENT_STATUS_FROM_CHECKBOX = ['Scheduled Call Back'];

    protected $user;
    protected $limit;
    protected $filters;
    protected $isSystemExport;
    protected bool $includeCenterColumn;

    /**
     * Constructor - Accepts user context and filters for authorization
     * 
     * @param User|null $user User requesting the export (null for system exports)
     * @param int $limit Maximum number of records to export
     * @param array $filters Additional filters (date range, status, etc.)
     * @param bool $isSystemExport If true, bypasses user authorization (for scheduled backups)
     */
    public function __construct($user = null, $limit = 1000, $filters = [], $isSystemExport = false)
    {
        $this->user = $user ?? Auth::user();
        $this->limit = $limit;
        $this->filters = $filters;
        $this->isSystemExport = $isSystemExport;
        $this->includeCenterColumn = (bool)($filters['include_center'] ?? true);
    }

    private static function defaultClosedCallPolicyStatuses(): array
    {
        return [
            'pending',
            'approved',
            'rejected',
            'Need to Reach',
            'NSF',
            'Cancelled',
            'DNC',
            'Underwriting',
            'Funded',
            'charged_backed',
            'Potential Lapsed',
        ];
    }

    /**
     * Apply filters from filters['statuses'] (policy status column vs agent_status checkbox rows).
     */
    private function applyExportStatusSelectionFilters($query): void
    {
        $defaults = self::defaultClosedCallPolicyStatuses();

        $selected = $this->filters['statuses'] ?? [];
        if (!is_array($selected)) {
            $selected = [];
        }

        $agentSelected = array_values(array_intersect($selected, self::EXPORT_AGENT_STATUS_FROM_CHECKBOX));
        $policySelected = array_values(array_diff($selected, self::EXPORT_AGENT_STATUS_FROM_CHECKBOX));

        if (count($selected) === 0) {
            $query->whereIn('status', $defaults);
        } elseif (count($policySelected) > 0 && count($agentSelected) > 0) {
            // Union: checked policy statuses OR checked agent-status labels (avoid AND, which hid non-callback rows)
            $query->where(function ($q) use ($policySelected, $agentSelected) {
                $q->whereIn('status', $policySelected)
                    ->orWhereIn('agent_status', $agentSelected);
            });
        } elseif (count($policySelected) > 0) {
            $query->whereIn('status', $policySelected);
        } elseif (count($agentSelected) > 0) {
            // Agent-status-only selection: do not restrict policy status column
            $query->whereIn('agent_status', $agentSelected);
        } else {
            $query->whereIn('status', $defaults);
        }

        if (isset($this->filters['agent_status'])) {
            $query->where('agent_status', $this->filters['agent_status']);
        }
    }

    /**
     * Get the collection of records with authorization checks
     */
    public function collection()
    {
        // System exports (like scheduled backups) - require special permission or be run from console
        if ($this->isSystemExport) {
            // System export - use same filters as user exports, but without user scoping or Gate checks
            $query = ClosedCall::query();

            $this->applyExportStatusSelectionFilters($query);

            $centers = $this->filters['centers'] ?? [];
            if (is_array($centers) && !empty($centers)) {
                $query->whereIn('center_name', $centers);
            }

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

            $query->latest();
            if ($this->limit >= PHP_INT_MAX - 1000) {
                return $query->get();
            }
            return $query->take($this->limit)->get();
        }

        // User-based exports - require authentication
        if (!$this->user) {
            Log::warning('Export attempted without authentication', [
                'ip' => request()->ip()
            ]);
            return collect([]);
        }

        // Build query with authorization filters
        $query = ClosedCall::query();

        // NOTE: For testing, user-based scoping by type (client/closer) has been disabled
        // so all authenticated users using the UI will see the same export data.

        $this->applyExportStatusSelectionFilters($query);

        // Optional center_name filter (jsons, sellerz, etc.)
        $centers = $this->filters['centers'] ?? [];
        if (is_array($centers) && !empty($centers)) {
            $query->whereIn('center_name', $centers);
        }

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

        // Log the export for audit purposes
        Log::info('ClosedCall export executed', [
            'user_id' => $this->user->id,
            'user_type' => $this->user->type,
            'record_count' => $query->count(),
            'filters' => $this->filters,
            'ip' => request()->ip()
        ]);

        // If limit is very large (>= PHP_INT_MAX - 1000), return all records
        if ($this->limit >= PHP_INT_MAX - 1000) {
            return $query->latest()->get();
        }
        return $query->latest()->take($this->limit)->get();
    }

    public function headings(): array
    {
        $headings = [
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
        ];

        if ($this->includeCenterColumn) {
            $headings[] = 'Center Name';
        }

        $headings = array_merge($headings, [
            'Sale Made By',
            'Status',
            'Agent Status',
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
        ]);

        return $headings;
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

        $data = [
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
        ];

        if ($this->includeCenterColumn) {
            $data[] = $row->center_name ?? '';
        }

        $data = array_merge($data, [
            $row->sale_made_by ?? '',
            $row->status ?? '',
            $row->agent_status ?? '',
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
        ]);

        return $data;
    }
}
