<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OutsourceClosedCall;
use App\Models\Team;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Client;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;
use App\Models\CloserTeam;
class OutsourceCloserController extends Controller
{

    public function create()
{
    $user = auth()->user();

    $team = CloserTeam::whereHas('members', function ($q) use ($user) {
        $q->where('users.id', $user->id);
    })->first();

    if ($team) {
        $closers = $team->members()->orderBy('name', 'asc')->get();
    } else {
        $closers = collect([$user]);
    }

    return view('outsource-closer.create', compact('closers'));
}
    public function store(Request $request)
    {
        try {
            $closedCall = new OutsourceClosedCall;
    
            $closedCall->customer_full_name = $request->customer_full_name;
            $closedCall->phone_number = $request->phone_number;
            $closedCall->alternate_phone_number = $request->alternate_phone_number;
            $closedCall->cx_email = $request->cx_email;
            $closedCall->address = $request->address;
            $closedCall->city = $request->city;
            $closedCall->state = $request->state;
            $closedCall->zip_code = $request->zip_code;
            $closedCall->gender = $request->gender;
            $closedCall->martial_status = $request->martial_status;
            $closedCall->age = $request->age;
            $closedCall->dob = $request->dob;
            $closedCall->palce_of_birth = $request->palce_of_birth;
            $closedCall->height = $request->height;
            $closedCall->weight = $request->weight;
            $closedCall->closername = $request->closername;
            $closedCall->social_security = $request->social_security;
            $closedCall->smoker = $request->smoker;
            $closedCall->health_condition = $request->health_condition;
            $closedCall->medication = $request->medication;
            $closedCall->hospital_name = $request->hospital_name;
            $closedCall->hospital_address = $request->hospital_address;
            $closedCall->physician_name = $request->physician_name;
            $closedCall->monthly_premium = $request->monthly_premium;
            $closedCall->carrier = $request->carrier;
            $closedCall->coverage_plan = $request->coverage_plan;
            $closedCall->customer_eligibility = $request->customer_eligibility;
            $closedCall->beneficiary = $request->beneficiary;
            $closedCall->beneficiary_relation = $request->beneficiary_relation;
            $closedCall->beneficiary_phone = $request->beneficiary_phone;
            $closedCall->beneficiary_dob = $request->beneficiary_dob;
            $closedCall->payor = $request->payor;
            $closedCall->bank_name = $request->bank_name;
            $closedCall->bank_address = $request->bank_address;
            $closedCall->routing_number = $request->routing_number;
            $closedCall->bank_account_number = $request->bank_account_number;
            $closedCall->debit_card_direct_express_no = $request->debit_card_direct_express_no;
            $closedCall->debit_card_direct_express_expiration = $request->debit_card_direct_express_expiration;
            $closedCall->debit_card_direct_express_cvv = $request->debit_card_direct_express_cvv;
            $closedCall->account_type = $request->account_type;
            $closedCall->initial_draft_date = $request->initial_draft_date;
            $closedCall->future_draft_date = $request->future_draft_date;
            $closedCall->underwriter_name = $request->underwriter_name;
            $closedCall->remarks = $request->remarks;
            $closedCall->closer_id = auth()->user()->id;
            $closedCall->junior_closer_name = $request->junior_closer_name;
            $closedCall->center_name = $request->center_name;
            $closedCall->sale_made_by = $request->sale_made_by;
            $closedCall->agent_status = $request->agent_status;
            $closedCall->underwriter_name = $request->underwriter_name;
    
            $closedCall->save();
    
            return redirect()->route('outsource.closerview')->with('success', 'Form submitted successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function closer_reports()
    {
        $user = auth()->user();
        $threeHoursLater = Carbon::today()->addHours(0);

        $pendingCount = OutsourceClosedCall::where('closer_id', $user->id)
            ->where('status', 'pending')
            ->count();

        $approvedCount = OutsourceClosedCall::where('closer_id', $user->id)
            ->where('status', 'approved')
            ->count();

        $rejected = OutsourceClosedCall::where('closer_id', $user->id)
            ->where('status', 'rejected')
            ->count();

        $funded = OutsourceClosedCall::where('closer_id', $user->id)
            ->where('status', 'funded')
            ->count();

        $charged_backed = OutsourceClosedCall::where('closer_id', $user->id)
            ->where('status', 'charged_backed')
            ->count();

        $DNF = OutsourceClosedCall::where('closer_id', $user->id)
            ->where('status', 'DNF')
            ->count();

        $Cancelled = OutsourceClosedCall::where('closer_id', $user->id)
            ->where('status', 'Cancelled')
            ->count();

        $NSF = OutsourceClosedCall::where('closer_id', $user->id)
            ->where('status', 'NSF')
            ->count();

        $DNC = OutsourceClosedCall::where('closer_id', $user->id)
            ->where('status', 'DNC')
            ->count();

        $Underwriting = OutsourceClosedCall::where('closer_id', $user->id)
            ->where('status', 'Underwriting')
            ->count();

        $NeedtoReach = OutsourceClosedCall::where('closer_id', $user->id)
            ->where('status', 'Need to Reach')
            ->count();

        $usersWithTotalClosedCalls = User::where('type', 'closer')
            ->withCount([
                'outsourceClosedCalls' => function ($query) use ($threeHoursLater) {
                    $query->where('created_at', '>=', $threeHoursLater);
                }
            ])
            ->get();

        $currentMonth = Carbon::now()->startOfMonth();
        $pending_monthly = OutsourceClosedCall::where('closer_id', $user->id)
            ->where('status', 'pending')
            ->where('created_at', '>=', $currentMonth)
            ->count();

        $approved_monthly = OutsourceClosedCall::where('closer_id', $user->id)
            ->where('status', 'approved')
            ->where('created_at', '>=', $currentMonth)
            ->count();

        $rejected_monthly = OutsourceClosedCall::where('closer_id', $user->id)
            ->where('status', 'rejected')
            ->where('created_at', '>=', $currentMonth)
            ->count();

        $funded_monthly = OutsourceClosedCall::where('closer_id', $user->id)
            ->where('status', 'funded')
            ->where('created_at', '>=', $currentMonth)
            ->count();

        $charged_backed_monthly = OutsourceClosedCall::where('closer_id', $user->id)
            ->where('status', 'charged_backed')
            ->where('created_at', '>=', $currentMonth)
            ->count();

        $userStats = OutsourceClosedCall::select('closer_id')
            ->selectRaw('count(*) as total')
            ->selectRaw("sum(case when status = 'pending' then 1 else 0 end) as pending")
            ->selectRaw("sum(case when status = 'approved' then 1 else 0 end) as approved")
            ->selectRaw("sum(case when status = 'rejected' then 1 else 0 end) as rejected")
            ->selectRaw("sum(case when status = 'funded' then 1 else 0 end) as funded")
            ->selectRaw("sum(case when status = 'charged_backed' then 1 else 0 end) as charged_backed")
            ->selectRaw("sum(case when status = 'DNF' then 1 else 0 end) as DNF")
            ->selectRaw("sum(case when status = 'Cancelled' then 1 else 0 end) as Cancelled")
            ->selectRaw("sum(case when status = 'NSF' then 1 else 0 end) as NSF")
            ->selectRaw("sum(case when status = 'DNC' then 1 else 0 end) as DNC")
            ->selectRaw("sum(case when status = 'Underwriting' then 1 else 0 end) as Underwriting")
            ->selectRaw("sum(case when status = 'Need to Reach' then 1 else 0 end) as NeedtoReach")
            ->where('created_at', '>=', $currentMonth)
            ->groupBy('closer_id')
            ->get();

        $closerUsersWithTotalClosedCalls = User::where('type', 'closer')
            ->withCount([
                'outsourceClosedCalls' => function ($query) {
                    $query->whereMonth('created_at', Carbon::now()->month);
                }
            ])
            ->get();

        return view('outsource-closer.reports', compact(
            'pendingCount',
            'approvedCount',
            'rejected',
            'funded',
            'charged_backed',
            'DNF',
            'Cancelled',
            'NSF',
            'DNC',
            'Underwriting',
            'NeedtoReach',
            'usersWithTotalClosedCalls',
            'closerUsersWithTotalClosedCalls',
            'pending_monthly',
            'charged_backed_monthly',
            'funded_monthly',
            'rejected_monthly',
            'userStats',
            'approved_monthly'
        ));
    }

    public function client_index(Request $request)
    {
        $perPage = $request->input('per_page', 5);
        $search = $request->input('search');
        $statusFilter = $request->input('status_filter');

        $query = OutsourceClosedCall::query();

        if (auth()->user()->type === 'client') {
            $authUserId = auth()->user()->id;
            $authUserEmail = auth()->user()->email;
            
            $client = Client::where('email', $authUserEmail)->first();
            
            if ($client) {
                $clientId = $client->id;
                $associatedUserIds = User::where('type', 'client')
                    ->where('client_id', $clientId)
                    ->pluck('id')
                    ->toArray();
                
                $associatedUserIds[] = $authUserId;
                $associatedUserIds = array_unique($associatedUserIds);
                
                if (!empty($associatedUserIds)) {
                    $query->whereIn('clients_id', $associatedUserIds);
                } else {
                    $query->where('id', 0);
                }
            } else {
                $query->where('clients_id', $authUserId);
            }
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('customer_full_name', 'LIKE', "%{$search}%")
                    ->orWhere('address', 'LIKE', "%{$search}%")
                    ->orWhere('city', 'LIKE', "%{$search}%")
                    ->orWhere('state', 'LIKE', "%{$search}%")
                    ->orWhere('remarks', 'LIKE', "%{$search}%")
                    ->orWhere('carrier', 'LIKE', "%{$search}%")
                    ->orWhere('coverage_plan', 'LIKE', "%{$search}%")
                    ->orWhere('physician_name', 'LIKE', "%{$search}%")
                    ->orWhere('gender', 'LIKE', "%{$search}%")
                    ->orWhere('martial_status', 'LIKE', "%{$search}%")
                    ->orWhere('hospital_name', 'LIKE', "%{$search}%")
                    ->orWhere('center_name', 'LIKE', "%{$search}%")
                    ->orWhere('status', 'LIKE', "%{$search}%")
                    ->orWhere('recording_id', 'LIKE', "%{$search}%")
                    ->orWhere('hippa_id', 'LIKE', "%{$search}%")
                    ->orWhere('policy_id', 'LIKE', "%{$search}%")
                    ->orWhere('recording_status', 'LIKE', "%{$search}%");
            });
        }
        
        if ($statusFilter && $statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }
        
        $allStatuses = OutsourceClosedCall::distinct()->pluck('status')->filter()->sort();
        $closedCalls = $query->orderBy('created_at', 'desc')->paginate($perPage);
        
        if ($request->ajax()) {
            return response()->json([
                'table_body' => view('outsource-closer.client-view-cards', compact('closedCalls'))->render(),
                'pagination_links' => $closedCalls->appends([
                    'per_page' => $perPage, 
                    'search' => $search,
                    'status_filter' => $statusFilter
                ])->links('pagination::bootstrap-5')->toHtml()
            ]);
        }

        return view('outsource-closer.client-view', [
            'closedCalls' => $closedCalls,
            'perPage' => $perPage,
            'search' => $search,
            'statusFilter' => $statusFilter,
            'allStatuses' => $allStatuses
        ]);
    }

    public function salesagentshow(Request $request)
    {
        if (!auth()->user()->can('agent sales')) {
            abort(403);
        }
        $perPage = $request->get('per_page', 50);
        $search = $request->get('search');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $query = OutsourceClosedCall::query();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('customer_full_name', 'LIKE', "%{$search}%")
                  ->orWhere('state', 'LIKE', "%{$search}%")
                  ->orWhere('closername', 'LIKE', "%{$search}%")
                  ->orWhere('underwriter_name', 'LIKE', "%{$search}%")
                  ->orWhere('clients_comment', 'LIKE', "%{$search}%")
                  ->orWhere('carrier', 'LIKE', "%{$search}%")
                  ->orWhere('status', 'LIKE', "%{$search}%")
                  ->orWhere('monthly_premium', 'LIKE', "%{$search}%")
                  ->orWhere('junior_closer_name', 'LIKE', "%{$search}%")
                  ->orWhere('juniorcloser2', 'LIKE', "%{$search}%")
                  ->orWhere('teamname', 'LIKE', "%{$search}%")
                  ->orWhere('agentname', 'LIKE', "%{$search}%")
                  ->orWhere('id', 'LIKE', "%{$search}%");
            });
        }

        if ($startDate && $endDate) {
            try {
                $startDate = Carbon::createFromFormat('Y-m-d', $startDate)->startOfDay();
                $endDate = Carbon::createFromFormat('Y-m-d', $endDate)->endOfDay();
                $query->whereBetween('created_at', [$startDate, $endDate]);
            } catch (\Exception $e) {
                Log::error('Invalid date format in salesagentshow: ' . $e->getMessage());
            }
        } elseif ($startDate) {
            try {
                $startDate = Carbon::createFromFormat('Y-m-d', $startDate)->startOfDay();
                $query->where('created_at', '>=', $startDate);
            } catch (\Exception $e) {
                Log::error('Invalid start date format: ' . $e->getMessage());
            }
        } elseif ($endDate) {
            try {
                $endDate = Carbon::createFromFormat('Y-m-d', $endDate)->endOfDay();
                $query->where('created_at', '<=', $endDate);
            } catch (\Exception $e) {
                Log::error('Invalid end date format: ' . $e->getMessage());
            }
        }

        $allowedStatuses = ['Sale made'];
        $closedCalls = $query->whereIn('agent_status', $allowedStatuses)
                            ->orderBy('created_at', 'desc')
                            ->paginate($perPage);
        
        $closedCalls->appends($request->all());

        $currentMonth = Carbon::now()->startOfMonth();
        $pendingCount = OutsourceClosedCall::where('status', 'pending')
            ->where('created_at', '>=', $currentMonth)
            ->count();

        return view('outsource-closer.salesagentshow', compact('closedCalls', 'pendingCount'));
    }

    public function exportSalesAgentData(Request $request)
    {
        if (!auth()->user()->can('agent sales')) {
            abort(403);
        }
        $search = $request->get('search');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $query = OutsourceClosedCall::query();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('customer_full_name', 'LIKE', "%{$search}%")
                  ->orWhere('state', 'LIKE', "%{$search}%")
                  ->orWhere('closername', 'LIKE', "%{$search}%")
                  ->orWhere('underwriter_name', 'LIKE', "%{$search}%")
                  ->orWhere('clients_comment', 'LIKE', "%{$search}%")
                  ->orWhere('carrier', 'LIKE', "%{$search}%")
                  ->orWhere('status', 'LIKE', "%{$search}%")
                  ->orWhere('monthly_premium', 'LIKE', "%{$search}%")
                  ->orWhere('junior_closer_name', 'LIKE', "%{$search}%")
                  ->orWhere('juniorcloser2', 'LIKE', "%{$search}%")
                  ->orWhere('teamname', 'LIKE', "%{$search}%")
                  ->orWhere('agentname', 'LIKE', "%{$search}%")
                  ->orWhere('id', 'LIKE', "%{$search}%");
            });
        }

        if ($startDate && $endDate) {
            try {
                $startDate = Carbon::createFromFormat('Y-m-d', $startDate)->startOfDay();
                $endDate = Carbon::createFromFormat('Y-m-d', $endDate)->endOfDay();
                $query->whereBetween('created_at', [$startDate, $endDate]);
            } catch (\Exception $e) {
                Log::error('Invalid date format in export: ' . $e->getMessage());
            }
        } elseif ($startDate) {
            try {
                $startDate = Carbon::createFromFormat('Y-m-d', $startDate)->startOfDay();
                $query->where('created_at', '>=', $startDate);
            } catch (\Exception $e) {
                Log::error('Invalid start date format: ' . $e->getMessage());
            }
        } elseif ($endDate) {
            try {
                $endDate = Carbon::createFromFormat('Y-m-d', $endDate)->endOfDay();
                $query->where('created_at', '<=', $endDate);
            } catch (\Exception $e) {
                Log::error('Invalid end date format: ' . $e->getMessage());
            }
        }

        $allowedStatuses = ['Sale made'];
        $closedCalls = $query->whereIn('agent_status', $allowedStatuses)
                            ->orderBy('created_at', 'desc')
                            ->get();

        $filename = 'outsource_sales_agent_data_' . date('Y_m_d_H_i_s');
        if ($startDate && $endDate) {
            $filename .= '_' . $startDate->format('Y_m_d') . '_to_' . $endDate->format('Y_m_d');
        }
        if ($search) {
            $filename .= '_filtered';
        }
        $filename .= '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($closedCalls, $search, $startDate, $endDate) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, ['Outsource Sales Agent Data Export']);
            fputcsv($file, ['Generated: ' . now()->format('Y-m-d H:i:s')]);
            fputcsv($file, ['Total Records: ' . $closedCalls->count()]);
            
            if ($search) {
                fputcsv($file, ['Search Filter: ' . $search]);
            }
            
            if ($startDate && $endDate) {
                fputcsv($file, ['Date Range: ' . $startDate->format('Y-m-d') . ' to ' . $endDate->format('Y-m-d')]);
            } elseif ($startDate) {
                fputcsv($file, ['Start Date: ' . $startDate->format('Y-m-d')]);
            } elseif ($endDate) {
                fputcsv($file, ['End Date: ' . $endDate->format('Y-m-d')]);
            }
            
            fputcsv($file, []);
            
            fputcsv($file, [
                'ID',
                'Date (PKT)',
                'Time (PKT)',
                'Customer Name',
                'State',
                'Monthly Premium',
                'Coverage Plan',
                'Customer Eligibility',
                'Carrier',
                'Closer Name',
                'Client Comments',
                'Underwriter',
                'Status',
                'Center Name',
                'Junior Closer',
                'Team Name',
                'Agent Name',
                'Lead ID',
                'Dialer',
                'List 1',
                'List 2',
                'Initial Draft Date',
                'Future Draft Date'
            ]);
            
            foreach ($closedCalls as $call) {
                $createdAt = $call->created_at->setTimezone('Asia/Karachi');
                
                $initialDraftDate = 'N/A';
                if ($call->initial_draft_date) {
                    try {
                        $initialDraftDate = $call->initial_draft_date->format('Y-m-d');
                    } catch (\Exception $e) {
                        $initialDraftDate = 'N/A';
                    }
                }
                
                $futureDraftDate = 'N/A';
                if ($call->future_draft_date) {
                    try {
                        $futureDraftDate = $call->future_draft_date->format('Y-m-d');
                    } catch (\Exception $e) {
                        $futureDraftDate = 'N/A';
                    }
                }
                
                fputcsv($file, [
                    $call->id,
                    $createdAt->format('Y-m-d'),
                    $createdAt->format('H:i:s'),
                    $call->customer_full_name ?? 'N/A',
                    $call->state ?? 'N/A',
                    $call->monthly_premium ?? 'N/A',
                    $call->coverage_plan ?? 'N/A',
                    $call->customer_eligibility ?? 'N/A',
                    $call->carrier ?? 'N/A',
                    $call->closername ?? 'N/A',
                    $call->clients_comment ?? 'N/A',
                    $call->client->name ?? 'N/A',
                    $call->status ?? 'N/A',
                    $call->center_name ?? 'N/A',
                    $call->juniorcloser->name ?? $call->junior_closer_name ?? 'N/A',
                    $call->teamname ?? 'N/A',
                    $call->agentname ?? 'N/A',
                    $call->lead_id ?? 'N/A',
                    $call->dialer_name_new ?? 'N/A',
                    $call->list_id_1 ?? 'N/A',
                    $call->list_id_2 ?? 'N/A',
                    $initialDraftDate,
                    $futureDraftDate
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function index(Request $request)
    {
       

        $perPage = $request->input('per_page', 5);
        $search = $request->input('search');

        $query = OutsourceClosedCall::query();

        if (auth()->user()->type === 'client') {
            $authUserEmail = auth()->user()->email;
            $client = Client::where('email', $authUserEmail)->first();
            
            if ($client) {
                $clientId = $client->id;
                $associatedUserIds = User::where('type', 'client')
                    ->where('client_id', $clientId)
                    ->pluck('id')
                    ->toArray();
                
                if (!empty($associatedUserIds)) {
                    $query->whereIn('clients_id', $associatedUserIds);
                } else {
                    $query->where('id', 0);
                }
            } else {
                $query->where('clients_id', auth()->user()->id);
            }
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('customer_full_name', 'LIKE', "%{$search}%")
                    ->orWhere('address', 'LIKE', "%{$search}%")
                    ->orWhere('city', 'LIKE', "%{$search}%")
                    ->orWhere('state', 'LIKE', "%{$search}%")
                    ->orWhere('remarks', 'LIKE', "%{$search}%")
                    ->orWhere('carrier', 'LIKE', "%{$search}%")
                    ->orWhere('coverage_plan', 'LIKE', "%{$search}%")
                    ->orWhere('physician_name', 'LIKE', "%{$search}%")
                    ->orWhere('gender', 'LIKE', "%{$search}%")
                    ->orWhere('martial_status', 'LIKE', "%{$search}%")
                    ->orWhere('hospital_name', 'LIKE', "%{$search}%")
                    ->orWhere('center_name', 'LIKE', "%{$search}%")
                    ->orWhere('status', 'LIKE', "%{$search}%");
            });
        }
        
        $allowedStatuses = ['Sale made'];
        $closedCalls = $query->whereIn('agent_status', $allowedStatuses)->paginate($perPage);
        
        if ($request->ajax()) {
            return response()->json([
                'table_body' => view('outsource-closer.table-body', compact('closedCalls'))->render(),
                'pagination_links' => $closedCalls->appends(['per_page' => $perPage, 'search' => $search])->links('pagination::bootstrap-5')->toHtml()
            ]);
        }

        return view('outsource-closer.index', [
            'closedCalls' => $closedCalls,
            'perPage' => $perPage,
            'search' => $search
        ]);
    }

    public function closerview()
    {
        $userId = auth()->user()->id;
        $employee = Auth::user();
        $dialer_id = $employee->dialer_id;

        $closedCalls = OutsourceClosedCall::where('closer_id', $userId)->paginate(25);
    
        $currentMonth = Carbon::now()->startOfMonth();
        $pendingCount = OutsourceClosedCall::where('status', 'pending')
            ->where('created_at', '>=', $currentMonth)
            ->count();
    
        return view('outsource-closer.closerview', compact('closedCalls', 'pendingCount', 'dialer_id'));
    }

    public function clientview()
    {
        $userName = auth()->user()->name;
        $underwriterPrefix = explode(' ', $userName)[0];
    
        $closedCalls = OutsourceClosedCall::where('underwriter_name', 'like', "$underwriterPrefix%")
            ->paginate(5);
    
        $currentMonth = Carbon::now()->startOfMonth();
        $pendingCount = OutsourceClosedCall::where('status', 'pending')
            ->where('created_at', '>=', $currentMonth)
            ->count();
    
        return view('outsource-closer.clientview', compact('closedCalls', 'pendingCount'));
    }

    public function closers_stats()
    {
        $currentMonth = Carbon::now()->startOfMonth();
        $pendingCount = OutsourceClosedCall::where('status', 'pending')
            ->where('created_at', '>=', $currentMonth)
            ->count();
        $approvedCount = OutsourceClosedCall::where('status', 'approved')
            ->where('created_at', '>=', $currentMonth)
            ->count();
        $rejectedCount = OutsourceClosedCall::where('status', 'rejected')
            ->where('created_at', '>=', $currentMonth)
            ->count();
        $fundedCount = OutsourceClosedCall::where('status', 'funded')
            ->where('created_at', '>=', $currentMonth)
            ->count();
        $chargedbackedCount = OutsourceClosedCall::where('status', 'charged_backed')
            ->where('created_at', '>=', $currentMonth)
            ->count();
        $clientsCountMissing = OutsourceClosedCall::where(function ($query) {
            $query->whereNull('clients_id');
        })->count();

        $clientsCount = OutsourceClosedCall::where(function ($query) {
            $query->WhereNotNull('clients_id');
        })->count();

        $threeHoursLater = Carbon::today()->addHours(0);
        $usersWithTotalClosedCalls = User::where('type', 'closer')
            ->withCount([
                'outsourceClosedCalls' => function ($query) use ($threeHoursLater) {
                    $query->where('created_at', '>=', $threeHoursLater);
                }
            ])
            ->get();

        $currentMonth = Carbon::now()->month;
        $totalJsonsCount = OutsourceClosedCall::where('center_name', 'jsons')
            ->whereMonth('created_at', $currentMonth)
            ->count();
        $totalJsonsApproved = OutsourceClosedCall::where('center_name', 'jsons')
            ->where('status', 'approved')
            ->whereMonth('created_at', $currentMonth)
            ->count();
        $totalJsonsRejected = OutsourceClosedCall::where('center_name', 'jsons')
            ->where('status', 'rejected')
            ->whereMonth('created_at', $currentMonth)
            ->count();
        $totalJsonsChargedbacked = OutsourceClosedCall::where('center_name', 'jsons')
            ->where('status', 'charged_backed')
            ->whereMonth('created_at', $currentMonth)
            ->count();
        $totalSellersCount = OutsourceClosedCall::where('center_name', 'sellerz')
            ->whereMonth('created_at', $currentMonth)
            ->count();
        $totalSellerzApproved = OutsourceClosedCall::where('center_name', 'sellerz')
            ->where('status', 'approved')
            ->whereMonth('created_at', $currentMonth)
            ->count();
        $totalSellerzrejected = OutsourceClosedCall::where('center_name', 'sellerz')
            ->where('status', 'rejected')
            ->whereMonth('created_at', $currentMonth)
            ->count();
        $totalSellerzChargedbacked = OutsourceClosedCall::where('center_name', 'sellerz')
            ->where('status', 'charged_backed')
            ->whereMonth('created_at', $currentMonth)
            ->count();

        $currentMonth = Carbon::now()->startOfMonth();
        $userStats = OutsourceClosedCall::select('closer_id')
            ->selectRaw('count(*) as total')
            ->selectRaw("sum(case when status = 'pending' then 1 else 0 end) as pending")
            ->selectRaw("sum(case when status = 'approved' then 1 else 0 end) as approved")
            ->selectRaw("sum(case when status = 'rejected' then 1 else 0 end) as rejected")
            ->selectRaw("sum(case when status = 'funded' then 1 else 0 end) as funded")
            ->selectRaw("sum(case when status = 'charged_backed' then 1 else 0 end) as charged_backed")
            ->selectRaw("sum(case when status = 'DNF' then 1 else 0 end) as DNF")
            ->selectRaw("sum(case when status = 'Cancelled' then 1 else 0 end) as Cancelled")
            ->selectRaw("sum(case when status = 'NSF' then 1 else 0 end) as NSF")
            ->selectRaw("sum(case when status = 'DNC' then 1 else 0 end) as DNC")
            ->selectRaw("sum(case when status = 'Underwriting' then 1 else 0 end) as Underwriting")
            ->selectRaw("sum(case when status = 'Need to Reach' then 1 else 0 end) as NeedtoReach")
            ->where('created_at', '>=', $currentMonth)
            ->groupBy('closer_id')
            ->get();

        return view('outsource-closer.stats', compact(
            'pendingCount',
            'approvedCount',
            'rejectedCount',
            'fundedCount',
            'chargedbackedCount',
            'clientsCount',
            'clientsCountMissing',
            'usersWithTotalClosedCalls',
            'userStats',
            'totalJsonsCount',
            'totalJsonsApproved',
            'totalJsonsRejected',
            'totalSellersCount',
            'totalSellerzApproved',
            'totalSellerzrejected',
            'totalSellerzChargedbacked',
            'totalJsonsChargedbacked'
        ));
    }

    public function edit($id)
    {
        $update = OutsourceClosedCall::find($id);
        $closers = User::whereIn('type', ['closer', 'Project Manager'])->orderBy('name', 'asc')->get();
        $clients = Client::all();
        $users = User::where('type', 'client')->get();
    
        return view('outsource-closer.editform', [
            'update' => $update, 
            'clients' => $clients, 
            'closers' => $closers,
            'users' => $users
        ]);
    }

    public function getUsers($clientId)
    {
        // 🔒 Authorization check - prevent user enumeration
        $user = auth()->user();
        
        if (!$user) {
            Log::warning('Unauthorized getUsers attempt - no user (Outsource)', [
                'client_id' => $clientId,
                'ip' => request()->ip()
            ]);
            return response()->json(['error' => 'Unauthorized access.'], 403);
        }

        // Check if user has permission to view users
        $hasPermission = Gate::forUser($user)->allows('view users') || 
                        Gate::forUser($user)->allows('manage users') ||
                        in_array($user->type, ['super admin', 'admin', 'Director', 'Project Manager']);
        
        if (!$hasPermission) {
            Log::warning('Unauthorized getUsers attempt - no permission (Outsource)', [
                'user_id' => $user->id,
                'user_type' => $user->type,
                'client_id' => $clientId,
                'ip' => request()->ip()
            ]);
            return response()->json(['error' => 'You do not have permission to view users.'], 403);
        }

        // Validate client_id is numeric to prevent injection
        if (!is_numeric($clientId)) {
            Log::warning('Invalid client_id in getUsers (Outsource)', [
                'user_id' => $user->id,
                'client_id' => $clientId,
                'ip' => request()->ip()
            ]);
            return response()->json(['error' => 'Invalid client ID.'], 400);
        }

        // 🔒 Verify user has access to this specific client
        $hasAccess = false;
        
        if (in_array($user->type, ['super admin', 'admin', 'Director', 'Project Manager'])) {
            // Admins can access any client
            $hasAccess = true;
        } elseif ($user->type === 'client') {
            // Client users can only access their own client hierarchy
            $authUserEmail = $user->email;
            $client = Client::where('email', $authUserEmail)->first();
            
            if ($client) {
                // PARENT CLIENT LOGIC - check if requested clientId matches their client_id
                if ($client->id == $clientId) {
                    $hasAccess = true;
                } else {
                    // Check if requested clientId is one of their child users
                    $childUserIds = User::where('type', 'client')
                        ->where('client_id', $client->id)
                        ->pluck('id')
                        ->toArray();
                    
                    if (in_array($clientId, $childUserIds) || $user->id == $clientId) {
                        $hasAccess = true;
                    }
                }
            } else {
                // CHILD CLIENT LOGIC - can only access their own client_id
                if ($user->id == $clientId) {
                    $hasAccess = true;
                }
            }
        }
        
        if (!$hasAccess) {
            Log::warning('Unauthorized getUsers attempt - no access to client (Outsource)', [
                'user_id' => $user->id,
                'user_type' => $user->type,
                'requested_client_id' => $clientId,
                'ip' => request()->ip()
            ]);
            // Return empty array instead of error to prevent enumeration
            return response()->json([]);
        }

        // Get users who have the selected client_id
        $users = User::where('client_id', $clientId)
                     ->where('type', 'client')
                     ->select('id', 'name', 'email', 'type', 'client_id') // Only return safe fields
                     ->get();
        
        // Log the access for audit purposes
        Log::info('Users retrieved by client_id (Outsource)', [
            'user_id' => $user->id,
            'user_type' => $user->type,
            'requested_client_id' => $clientId,
            'result_count' => $users->count(),
            'ip' => request()->ip()
        ]);
        
        return response()->json($users);
    }

    public function update(Request $request, $id)
    {
        $update = OutsourceClosedCall::find($id);
        $update->customer_full_name = $request->customer_full_name;
        $update->phone_number = $request->phone_number;
        $update->alternate_phone_number = $request->alternate_phone_number;
        $update->cx_email = $request->cx_email;
        $update->address = $request->address;
        $update->city = $request->city;
        $update->state = $request->state;
        $update->zip_code = $request->zip_code;
        $update->gender = $request->gender;
        $update->martial_status = $request->martial_status;
        $update->age = $request->age;
        
        if (!empty($request->dob)) {
            $update->dob = date('Y-m-d', strtotime($request->dob));
        }

        $update->social_security = $request->social_security;
        $update->smoker = $request->smoker;
        $update->health_condition = $request->health_condition;
        $update->medication = $request->medication;
        $update->hospital_name = $request->hospital_name;
        $update->hospital_address = $request->hospital_address;
        $update->physician_name = $request->physician_name;
        $update->customer_eligibility = $request->customer_eligibility;
        $update->beneficiary = $request->beneficiary;
        $update->beneficiary_relation = $request->beneficiary_relation;
        $update->beneficiary_phone = $request->beneficiary_phone;
        
        if (!empty($request->beneficiary_dob)) {
            $update->beneficiary_dob = date('Y-m-d', strtotime($request->beneficiary_dob));
        }

        $update->payor = $request->payor;
        $update->bank_name = $request->bank_name;
        $update->bank_address = $request->bank_address;
        $update->routing_number = $request->routing_number;
        $update->bank_account_number = $request->bank_account_number;
        $update->debit_card_direct_express_no = $request->debit_card_direct_express_no;

        if (!empty($request->debit_card_direct_express_expiration)) {
            $update->debit_card_direct_express_expiration = date('Y-m-d', strtotime($request->debit_card_direct_express_expiration));
        }

        $update->debit_card_direct_express_cvv = $request->debit_card_direct_express_cvv;
        $update->account_type = $request->account_type;

        if (!empty($request->initial_draft_date)) {
            $update->initial_draft_date = date('Y-m-d', strtotime($request->initial_draft_date));
        }
        
        if (!empty($request->future_draft_date)) {
            $update->future_draft_date = date('Y-m-d', strtotime($request->future_draft_date));
        }
        
        $update->underwriter_name = $request->underwriter_name;
        $update->remarks = $request->remarks;
        $update->closer_id = $request->closer_id;
        $update->junior_closer_name = $request->junior_closer_name;
        $update->center_name = $request->center_name;
        $update->sale_made_by = $request->sale_made_by;
        $update->status = $request->status;
        $update->clients_comment = $request->clients_comment;
        $update->dialername = $request->dialername;
        $update->dialeragentname = $request->dialeragentname;
        $update->lead_id = $request->lead_id;
        $update->closername = $request->closername;
        $update->juniorcloser2 = $request->juniorcloser2;
        $update->list_id_1 = $request->list_id_1;
        $update->list_id_2 = $request->list_id_2;
        $update->monthly_premium = $request->monthly_premium;
        $update->coverage_plan = $request->coverage_plan;
        $update->carrier = $request->carrier;
        $update->clients_id = $request->clients_id;

        $update->save();

        return redirect()->route('outsource.index')->with('success', 'Outsource policy updated successfully.');
    }

    public function show($id)
    {
        $user = auth()->user();
        
        if ($user->type === 'client') {
            $closedCall = OutsourceClosedCall::findOrFail($id);
            
            $authUserEmail = $user->email;
            $client = Client::where('email', $authUserEmail)->first();
            
            if ($client) {
                $clientId = $client->id;
                $associatedUserIds = User::where('type', 'client')
                    ->where('client_id', $clientId)
                    ->pluck('id')
                    ->toArray();
                
                if (!in_array($closedCall->clients_id, $associatedUserIds)) {
                    abort(404, 'Record not found.');
                }
            } else {
                if ($closedCall->clients_id !== $user->id) {
                    abort(404, 'Record not found.');
                }
            }
            
            return view('outsource-closer.show', compact('closedCall'));
        }
        
        if (!$user->can('closedcall')) {
            abort(403, 'You don\'t have permission to view this page.');
        }
        
        $closedCall = OutsourceClosedCall::findOrFail($id);
        return view('outsource-closer.show', compact('closedCall'));
    }

    public function showagentsales($id)
    {
        $closedCall = OutsourceClosedCall::findOrFail($id);
        return view('outsource-closer.agentsales', compact('closedCall'));
    }

    public function clientindex()
    {
        $userName = auth()->user()->name;
        $underwriterPrefix = explode(' ', $userName)[0];
    
        $closedCalls = OutsourceClosedCall::where('underwriter_name', 'like', "$underwriterPrefix%")
            ->paginate(5);

        $userId = auth()->id();

        $pendingCount = OutsourceClosedCall::where('clients_id', $userId)
            ->where('status', 'pending')
            ->count();
        $approvedCount = OutsourceClosedCall::where('clients_id', $userId)
            ->where('status', 'approved')
            ->count();
        $rejectedCount = OutsourceClosedCall::where('clients_id', $userId)
            ->where('status', 'rejected')
            ->count();
        $fundedCount = OutsourceClosedCall::where('clients_id', $userId)
            ->where('status', 'funded')
            ->count();
        $chargedbackedCount = OutsourceClosedCall::where('clients_id', $userId)
            ->where('status', 'charged_backed')
            ->count();

        $currentMonth = Carbon::now()->startOfMonth();

        $aig = OutsourceClosedCall::where(function ($query) {
            $query->where('carrier', 'AIG');
        })
            ->where('created_at', '>=', $currentMonth)
            ->count();
        $muo = OutsourceClosedCall::where(function ($query) {
            $query->where('carrier', 'Mutual_of_Omaha');
        })
            ->where('created_at', '>=', $currentMonth)
            ->count();

        return view('outsource-closer.manage-policies', compact(
            'closedCalls',
            'pendingCount',
            'approvedCount',
            'rejectedCount',
            'fundedCount',
            'chargedbackedCount',
            'aig',
            'muo'
        ));
    }

    public function editclient($id)
    {
        $update = OutsourceClosedCall::find($id);
        $closers = User::where('type', 'closer')->get();
        $clients = User::where('type', 'client')->get();
        return view('outsource-closer.edit-policy', ['update' => $update, 'clients' => $clients, 'closers' => $closers]);
    }

    public function updateclient(Request $request, $id)
    {
        $update = OutsourceClosedCall::find($id);
        
        if (!$update) {
            return back()->with('error', 'Policy not found.');
        }
        
        $update->customer_full_name = $request->customer_full_name;
        $update->phone_number = $request->phone_number;
        $update->alternate_phone_number = $request->alternate_phone_number;
        $update->cx_email = $request->cx_email;
        $update->address = $request->address;
        $update->city = $request->city;
        $update->state = $request->state;
        $update->zip_code = $request->zip_code;
        $update->gender = $request->gender;
        $update->martial_status = $request->martial_status;
        $update->age = $request->age;
        
        if (!empty($request->dob)) {
            $update->dob = date('Y-m-d', strtotime($request->dob));
        }

        $update->social_security = $request->social_security;
        $update->smoker = $request->smoker;
        $update->health_condition = $request->health_condition;
        $update->medication = $request->medication;
        $update->hospital_name = $request->hospital_name;
        $update->hospital_address = $request->hospital_address;
        $update->physician_name = $request->physician_name;
        $update->monthly_premium = $request->monthly_premium;
        $update->customer_eligibility = $request->customer_eligibility;
        $update->beneficiary = $request->beneficiary;
        $update->beneficiary_relation = $request->beneficiary_relation;
        $update->beneficiary_phone = $request->beneficiary_phone;
        
        if (!empty($request->beneficiary_dob)) {
            $update->beneficiary_dob = date('Y-m-d', strtotime($request->beneficiary_dob));
        }

        $update->payor = $request->payor;
        $update->bank_name = $request->bank_name;
        $update->bank_address = $request->bank_address;
        $update->routing_number = $request->routing_number;
        $update->bank_account_number = $request->bank_account_number;
        $update->debit_card_direct_express_no = $request->debit_card_direct_express_no;

        if (!empty($request->debit_card_direct_express_expiration)) {
            $update->debit_card_direct_express_expiration = date('Y-m-d', strtotime($request->debit_card_direct_express_expiration));
        }

        $update->debit_card_direct_express_cvv = $request->debit_card_direct_express_cvv;
        $update->account_type = $request->account_type;

        if (!empty($request->initial_draft_date)) {
            $update->initial_draft_date = date('Y-m-d', strtotime($request->initial_draft_date));
        }
        
        if (!empty($request->future_draft_date)) {
            $update->future_draft_date = date('Y-m-d', strtotime($request->future_draft_date));
        }
        
        $update->remarks = $request->remarks;
        $update->status = $request->status;
        $update->clients_comment = $request->clients_comment;
        $update->recording_id = $request->recording_id;
        $update->hippa_id = $request->hippa_id;
        $update->policy_id = $request->policy_id;
        $update->recording_status = $request->recording_status;
        $update->signature_type = $request->signature_type;
        $update->call_id = $request->call_id;
        $update->carrier = $request->carrier;
        $update->client_name_2 = $request->client_name_2;

        $user = auth()->user();

        if ($user->type === 'client') {
            $update->clients_id = $user->id;
        }

        $update->save();

        return back()->with('success', 'Policy updated successfully!');
    }

    public function addsaleagentrecord($id)
    {
        $update = OutsourceClosedCall::find($id);
        $closers = User::where('type', 'closer')->get();
        $clients = User::where('type', 'client')->get();
        return view('outsource-closer.salesagent', ['update' => $update, 'clients' => $clients, 'closers' => $closers]);
    }

    public function storesalesagentrecord()
    {
        $closers = User::where('type', 'closer')->get();
        return view('outsource-closer.salesagent', compact('closers'));
    }

    public function editdialer($id)
    {
        $update = OutsourceClosedCall::find($id);
        $dialers = User::where('type', 'Dialer Support')->get();
        $agents = User::where('type', 'Avatar')->orderBy('name', 'asc')->get();
        $closers = User::where('type', 'closer')->get();
        $teams = DB::table('teams')->orderBy('name', 'asc')->get();

        return view('outsource-closer.dialeredit', [
            'update' => $update,
            'dialers' => $dialers,
            'agents' => $agents,
            'closers' => $closers,
            'teams' => $teams
        ]);
    }

    public function updatedialer(Request $request, $id)
    {
        $update = OutsourceClosedCall::find($id);
       
        $update->dialername = $request->dialername;
        $update->dialeragentname = $request->dialeragentname;
        $update->agentname = $request->agentname;
        $update->teamname = $request->teamname;
        $update->lead_id = $request->lead_id;
        $update->closername = $request->closername;
        $update->juniorcloser2 = $request->juniorcloser2;
        $update->list_id_1 = $request->list_id_1;
        $update->list_id_2 = $request->list_id_2;
        $update->dialeragentname = $request->dialeragentname;
        $update->dialer_name_new = $request->dialer_name_new;

        $user = auth()->user();

        if ($user->type === 'client') {
            $update->clients_id = $user->id;
        }

        $update->save();

        return redirect()->route('outsource.salesagentshow')->with('success', 'Policy updated successfully.');
    }

    public function callback($id)
    {
        try {
            $closedCall = OutsourceClosedCall::findOrFail($id);
            $user = Auth::user();
            
            if (!$user || !$user->dialer_id) {
                return back()->with('error', 'User not authenticated or dialer ID not found.');
            }
            
            if (empty($closedCall->phone_number)) {
                return back()->with('error', 'Phone number not available for this call.');
            }
            
            if (!preg_match('/^\+?\d{10,15}$/', $closedCall->phone_number)) {
                return back()->with('error', 'Invalid phone number format.');
            }
            
            $api_url = env('DIALER_API_URL', 'https://jsons3.dialerhosting.com/agc/api.php');
            $api_params = [
                'source' => 'test',
                'user' => env('DIALER_API_USER', 'CRM305'),
                'pass' => env('DIALER_API_PASS', 'CPSswtUsrH7S76Q64'),
                'agent_user' => $user->dialer_id,
                'function' => 'external_dial',
                'value' => $closedCall->phone_number,
                'phone_code' => '1',
                'search' => 'YES',
                'preview' => 'NO',
                'focus' => 'YES'
            ];
            
            $client = new \GuzzleHttp\Client();
            $response = $client->get($api_url, [
                'query' => $api_params,
                'http_errors' => false
            ]);
            
            $result = json_decode($response->getBody(), true);
            
            Log::info('Outsource Callback initiated', [
                'user_id' => $user->id,
                'closed_call_id' => $id,
                'response' => $result
            ]);
            
            return back()->with([
                'success' => 'Call initiated successfully! Check your dialer interface.',
                'phone' => substr($closedCall->phone_number, 0, 3) . '-xxx-' . substr($closedCall->phone_number, -4)
            ]);
            
        } catch (\Exception $e) {
            Log::error('Outsource Callback error: ' . $e->getMessage(), [
                'id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Error initiating callback: ' . $e->getMessage());
        }
    }

    public function re_edit($id)
    {
        $update = OutsourceClosedCall::find($id);
        $closers = User::whereIn('type', ['closer', 'Project Manager'])->orderBy('name', 'asc')->get();
        $clients = Client::all();
        $users = User::where('type', 'client')->get();
    
        return view('outsource-closer.recall', [
            'update' => $update, 
            'clients' => $clients, 
            'closers' => $closers,
            'users' => $users
        ]);
    }

    public function re_update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'customer_full_name' => 'required|string|max:255',
                'phone_number' => 'required|digits:10',
                'alternate_phone_number' => 'nullable|digits:10',
                'cx_email' => 'nullable|email|max:255',
                'address' => 'nullable|string',
                'city' => 'required|string|max:255',
                'state' => 'nullable|string|size:2',
                'zip_code' => 'nullable|string|max:10',
                'gender' => 'nullable|in:male,female',
                'martial_status' => 'nullable|in:single,married,divorced,widowed,separated',
                'age' => 'required|integer|min:1|max:99',
                'dob' => 'nullable|date',
                'palce_of_birth' => 'nullable|string|max:255',
                'height' => 'nullable|string|max:10',
                'weight' => 'nullable|integer|min:1',
                'closername' => 'required|string|max:255',
                'social_security' => 'nullable|string|max:20',
                'smoker' => 'nullable|in:yes,no',
                'health_condition' => 'nullable|string',
                'medication' => 'nullable|string',
                'hospital_name' => 'nullable|string|max:255',
                'hospital_address' => 'nullable|string',
                'physician_name' => 'nullable|string|max:255',
                'monthly_premium' => 'nullable|regex:/^\d+(\.\d{1,2})?$/',
                'carrier' => 'nullable|string|max:255',
                'coverage_plan' => 'nullable|integer|min:2000|max:50000',
                'customer_eligibility' => 'nullable|in:level,Graded/Modified,Guaranteed Issue',
                'beneficiary' => 'nullable|string|max:255',
                'beneficiary_relation' => 'nullable|string|max:255',
                'beneficiary_phone' => 'nullable|digits:10',
                'beneficiary_dob' => 'nullable|date',
                'payor' => 'nullable|string|max:255',
                'bank_name' => 'nullable|string|max:255',
                'bank_address' => 'nullable|string',
                'routing_number' => 'nullable|string|max:20',
                'bank_account_number' => 'nullable|string|max:20',
                'debit_card_direct_express_no' => 'nullable|string|max:20',
                'debit_card_direct_express_expiration' => 'nullable|string|max:10',
                'debit_card_direct_express_cvv' => 'nullable|string|max:4',
                'account_type' => 'nullable|in:Savings Account,Checking Account,Direct Express Card,Debit Card',
                'initial_draft_date' => 'nullable|date',
                'future_draft_date' => 'nullable|date',
                'remarks' => 'nullable|string',
                'junior_closer_name' => 'nullable|string|max:255',
                'center_name' => 'nullable|in:sellerz,jsons',
                'sale_made_by' => 'nullable|in:CallBack,Junior Closer\'s Xfer,livetransfer,On Lead,retention,WinBack',
                'agent_status' => 'nullable|in:pending,Dropped Call,Sale made,Scheduled Call Back',
                'underwriter_name' => 'nullable|string|max:255',
            ]);

            $closedCall = OutsourceClosedCall::findOrFail($id);

            $closedCall->customer_full_name = $request->customer_full_name;
            $closedCall->phone_number = $request->phone_number;
            $closedCall->alternate_phone_number = $request->alternate_phone_number;
            $closedCall->cx_email = $request->cx_email;
            $closedCall->address = $request->address;
            $closedCall->city = $request->city;
            $closedCall->state = $request->state;
            $closedCall->zip_code = $request->zip_code;
            $closedCall->gender = $request->gender;
            $closedCall->martial_status = $request->martial_status;
            $closedCall->age = $request->age;
            $closedCall->dob = $request->dob;
            $closedCall->palce_of_birth = $request->palce_of_birth;
            $closedCall->height = $request->height;
            $closedCall->weight = $request->weight;
            $closedCall->closername = $request->closername;
            $closedCall->social_security = $request->social_security;
            $closedCall->smoker = $request->smoker;
            $closedCall->health_condition = $request->health_condition;
            $closedCall->medication = $request->medication;
            $closedCall->hospital_name = $request->hospital_name;
            $closedCall->hospital_address = $request->hospital_address;
            $closedCall->physician_name = $request->physician_name;
            $closedCall->monthly_premium = $request->monthly_premium;
            $closedCall->carrier = $request->carrier;
            $closedCall->coverage_plan = $request->coverage_plan;
            $closedCall->customer_eligibility = $request->customer_eligibility;
            $closedCall->beneficiary = $request->beneficiary;
            $closedCall->beneficiary_relation = $request->beneficiary_relation;
            $closedCall->beneficiary_phone = $request->beneficiary_phone;
            $closedCall->beneficiary_dob = $request->beneficiary_dob;
            $closedCall->payor = $request->payor;
            $closedCall->bank_name = $request->bank_name;
            $closedCall->bank_address = $request->bank_address;
            $closedCall->routing_number = $request->routing_number;
            $closedCall->bank_account_number = $request->bank_account_number;
            $closedCall->debit_card_direct_express_no = $request->debit_card_direct_express_no;
            $closedCall->debit_card_direct_express_expiration = $request->debit_card_direct_express_expiration;
            $closedCall->debit_card_direct_express_cvv = $request->debit_card_direct_express_cvv;
            $closedCall->account_type = $request->account_type;
            $closedCall->initial_draft_date = $request->initial_draft_date;
            $closedCall->future_draft_date = $request->future_draft_date;
            $closedCall->underwriter_name = $request->underwriter_name;
            $closedCall->remarks = $request->remarks;
            $closedCall->closer_id = auth()->user()->id;
            $closedCall->junior_closer_name = $request->junior_closer_name;
            $closedCall->center_name = $request->center_name;
            $closedCall->sale_made_by = $request->sale_made_by;
            $closedCall->agent_status = $request->agent_status;

            $closedCall->save();

            return redirect()->route('outsource.closerview')->with('success', 'Policy updated successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}