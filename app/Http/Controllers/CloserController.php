<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClosedCall;
use App\Models\Team;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Client; // Assuming you have a Client model
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;
use App\Models\OutsourceClosedCall;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Http\Requests\UpdateOwnClientRequest;


class CloserController extends Controller
{

    // closers setions starts here

    // closer form shows here
    public function create()
    {
        $closers = User::whereIn('type', ['closer', 'Project Manager'])->orderBy('name', 'asc')->get();
        return view('closer-section.create', compact('closers'));
    }








    // store method for closer form
    public function store(Request $request)
    {
        try {
            $isScheduledCallback = $request->input('agent_status') === 'Scheduled Call Back';

            // 🟩 Step 1: Validate incoming data
            $validated = $request->validate([
                'customer_full_name' => ($isScheduledCallback ? 'nullable' : 'required') . '|string|max:255',
                'phone_number' => 'required|string|max:20',
                'alternate_phone_number' => 'nullable|string|max:20',
                'cx_email' => 'nullable|email|max:255',
                'address' => 'nullable|string|max:255',
                'city' => 'nullable|string|max:100',
                'state' => 'nullable|string|max:100',
                'zip_code' => 'nullable|string|max:20',
                'gender' => 'nullable|string|max:10',
                'martial_status' => 'nullable|string|max:50',
                'age' => 'nullable|integer|min:0',
                'dob' => 'nullable|date',
                'palce_of_birth' => 'nullable|string|max:255',
                'height' => 'nullable|string|max:20',
                'weight' => 'nullable|string|max:20',
                'closername' => 'nullable|string|max:255',
                'social_security' => 'nullable|string|max:50',
                'smoker' => 'nullable|string|max:10',
                'health_condition' => 'nullable|string',
                'medication' => 'nullable|string',
                'hospital_name' => 'nullable|string|max:255',
                'hospital_address' => 'nullable|string|max:255',
                'physician_name' => 'nullable|string|max:255',
                'monthly_premium' => 'nullable|numeric|min:0',
                'carrier' => 'nullable|string|max:255',
                'coverage_plan' => 'nullable|string|max:255',
                'customer_eligibility' => 'nullable|string|max:255',
                'beneficiary' => 'nullable|string|max:255',
                'beneficiary_relation' => 'nullable|string|max:100',
                'beneficiary_phone' => 'nullable|string|max:20',
                'beneficiary_dob' => 'nullable|date',
                'payor' => 'nullable|string|max:255',
                'bank_name' => 'nullable|string|max:255',
                'bank_address' => 'nullable|string|max:255',
                'routing_number' => 'nullable|string|max:50',
                'bank_account_number' => 'nullable|string|max:50',
                'debit_card_direct_express_no' => 'nullable|string|max:50',
                'debit_card_direct_express_expiration' => 'nullable|string|max:10',
                'debit_card_direct_express_cvv' => 'nullable|string|max:10',
                'account_type' => 'nullable|string|max:50',
                'initial_draft_date' => 'nullable|date',
                'future_draft_date' => 'nullable|date',
                'underwriter_name' => 'nullable|string|max:255',
                'remarks' => 'nullable|string',
                'junior_closer_name' => 'nullable|string|max:255',
                'center_name' => 'nullable|string|max:255',
                'sale_made_by' => 'nullable|string|max:255',
                'agent_status' => 'required|string|max:100',
            ]);

            $hasBankDetails = $request->filled(['bank_name', 'routing_number', 'bank_account_number']);
            $hasDebitCardDetails = $request->filled(['debit_card_direct_express_no', 'debit_card_direct_express_expiration', 'debit_card_direct_express_cvv']);

            if (!$isScheduledCallback && !$hasBankDetails && !$hasDebitCardDetails) {
                return back()->withErrors([
                    'bank_or_card' => 'You must provide either bank details OR debit card details.',
                ])->withInput();
            }
           

            // 🟩 Step 3: Create the record
            $user = auth()->user();
            $validated['closer_id'] = $user->id;
            // center_id comes from authenticated user's center_id, not the form
            $validated['center_id'] = $user->center_id ?? null;

            ClosedCall::create($validated);

            // 🟩 Step 4: Redirect with success
            return redirect()->route('closer.closerview')->with('success', 'Form submitted successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }


    // create a new and enhanced store method with validation



    public function closer_reports()
    {
        $user = auth()->user();

        $threeHoursLater = Carbon::today()->addHours(0);
        // dd($threeHoursLater);


        $pendingCount = ClosedCall::where('closer_id', $user->id)
            ->where('status', 'pending')
            ->count();

        $approvedCount = ClosedCall::where('closer_id', $user->id)
            ->where('status', 'approved')
            ->count();

        $rejected = ClosedCall::where('closer_id', $user->id)
            ->where('status', 'rejected')
            ->count();

        $funded = ClosedCall::where('closer_id', $user->id)
            ->where('status', 'funded')
            ->count();

        $charged_backed = ClosedCall::where('closer_id', $user->id)
            ->where('status', 'charged_backed')
            ->count();
        $DNF = ClosedCall::where('closer_id', $user->id)
            ->where('status', 'DNF')
            ->count();
        $Cancelled = ClosedCall::where('closer_id', $user->id)
            ->where('status', 'Cancelled')
            ->count();
        $NSF = ClosedCall::where('closer_id', $user->id)
            ->where('status', 'NSF')
            ->count();
        $DNC = ClosedCall::where('closer_id', $user->id)
            ->where('status', 'DNC')
            ->count();
        $Underwriting = ClosedCall::where('closer_id', $user->id)
            ->where('status', 'Underwriting')
            ->count();
        $NeedtoReach = ClosedCall::where('closer_id', $user->id)
            ->where('status', 'Need to Reach')
            ->count();

        $usersWithTotalClosedCalls = User::where('type', 'closer')
            ->withCount([
                'closedCalls' => function ($query) use ($threeHoursLater) {
                    $query->where('created_at', '>=', $threeHoursLater);
                }
            ])
            ->get();



        // closers monthly report starts here



        $currentMonth = Carbon::now()->startOfMonth();
        $pending_monthly = ClosedCall::where('closer_id', $user->id)
            ->where('status', 'pending')
            ->where('created_at', '>=', $currentMonth)
            ->count();
        $approved_monthly = ClosedCall::where('closer_id', $user->id)
            ->where('status', 'approved')
            ->where('created_at', '>=', $currentMonth)
            ->count();
        $rejected_monthly = ClosedCall::where('closer_id', $user->id)
            ->where('status', 'rejected')
            ->where('created_at', '>=', $currentMonth)
            ->count();
        $funded_monthly = ClosedCall::where('closer_id', $user->id)
            ->where('status', 'funded')
            ->where('created_at', '>=', $currentMonth)
            ->count();
        $charged_backed_monthly = ClosedCall::where('closer_id', $user->id)
            ->where('status', 'charged_backed')
            ->where('created_at', '>=', $currentMonth)
            ->count();



        // closers monthly reports tables starts here

        $userStats = ClosedCall::select('closer_id')
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
            // Add more cases for other statuses if needed
            ->where('created_at', '>=', $currentMonth)
            ->groupBy('closer_id')
            ->get();
        // closers monthly reports tables ends here




        // closers monthly report ends here





        $closerUsersWithTotalClosedCalls = User::where('type', 'closer')
            ->withCount([
                'closedCalls' => function ($query) {
                    $query->whereMonth('created_at', Carbon::now()->month);
                }
            ])
            ->get();



        return view(
            'closer-section.reports',
            compact(
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
            )
        );
    }



    // all closed policies shows here


    public function client_index(Request $request)
    {
        $perPage = $request->input('per_page', 5);
        $search = $request->input('search');
        $statusFilter = $request->input('status_filter');
        $currentPage = LengthAwarePaginator::resolveCurrentPage() ?: 1;
        $path = $request->url();

        // Build base queries
        $query1 = ClosedCall::with(['client', 'closer', 'juniorcloser']);
        $query2 = OutsourceClosedCall::with(['client', 'closer', 'juniorcloser']);

        // ─── CLIENT FILTERING (apply BEFORE anything else) ───────────────────────
        if (auth()->user()->type === 'client') {
            $authUserId    = auth()->user()->id;
            $authUserEmail = auth()->user()->email;

            $client = \App\Models\Client::where('email', $authUserEmail)->first();

            if ($client) {
                // Parent client — include all child users
                $clientId = $client->id;
                $associatedUserIds = \App\Models\User::where('type', 'client')
                    ->where('client_id', $clientId)
                    ->pluck('id')
                    ->push($authUserId)   // include own ID
                    ->unique()
                    ->toArray();

                if (!empty($associatedUserIds)) {
                    $query1->whereIn('clients_id', $associatedUserIds);
                    $query2->whereIn('clients_id', $associatedUserIds);
                } else {
                    $query1->whereRaw('1 = 0');
                    $query2->whereRaw('1 = 0');
                }
            } else {
                // Child client — filter by own user ID only
                $query1->where('clients_id', $authUserId);
                $query2->where('clients_id', $authUserId);
            }
        }

        // ─── SEARCH FILTER ───────────────────────────────────────────────────────
        if ($search) {
            $searchCallback = function ($q) use ($search) {
                $q->where('customer_full_name', 'LIKE', "%{$search}%")
                    ->orWhere('address',          'LIKE', "%{$search}%")
                    ->orWhere('city',             'LIKE', "%{$search}%")
                    ->orWhere('state',            'LIKE', "%{$search}%")
                    ->orWhere('remarks',          'LIKE', "%{$search}%")
                    ->orWhere('carrier',          'LIKE', "%{$search}%")
                    ->orWhere('coverage_plan',    'LIKE', "%{$search}%")
                    ->orWhere('physician_name',   'LIKE', "%{$search}%")
                    ->orWhere('gender',           'LIKE', "%{$search}%")
                    ->orWhere('martial_status',   'LIKE', "%{$search}%")
                    ->orWhere('hospital_name',    'LIKE', "%{$search}%")
                    ->orWhere('center_name',      'LIKE', "%{$search}%")
                    ->orWhere('status',           'LIKE', "%{$search}%")
                    ->orWhere('recording_id',     'LIKE', "%{$search}%")
                    ->orWhere('hippa_id',         'LIKE', "%{$search}%")
                    ->orWhere('policy_id',        'LIKE', "%{$search}%")
                    ->orWhere('recording_status', 'LIKE', "%{$search}%");
            };

            $query1->where(fn($q) => $searchCallback($q));
            $query2->where(fn($q) => $searchCallback($q));
        }

        // ─── STATUS FILTER ───────────────────────────────────────────────────────
        if ($statusFilter && $statusFilter !== 'all') {
            $query1->where('status', $statusFilter);
            $query2->where('status', $statusFilter);
        }

        // ─── FETCH ALL MATCHING RECORDS (for correct counts) ─────────────────────
        $items1 = $query1->orderBy('created_at', 'desc')->get();
        $items2 = $query2->orderBy('created_at', 'desc')->get();

        // Merge and sort
        $allItems = $items1->merge($items2)->sortByDesc('created_at');

        // ─── STATUS GROUP COUNTS (over the FULL filtered set) ────────────────────
        $approvedStatuses = ['Funded', 'charged_backed', 'Approved', 'Potential Lapsed'];
        $pendingStatuses  = ['Pending', 'Underwriting', 'Need to Reach', 'NSF'];
        $rejectedStatuses = ['Rejected', 'DNC'];

        $totalCount    = $allItems->count();
        $approvedCount = $allItems->whereIn('status', $approvedStatuses)->count();
        $pendingCount  = $allItems->whereIn('status', $pendingStatuses)->count();
        $rejectedCount = $allItems->whereIn('status', $rejectedStatuses)->count();

        // Count every individual status for the detailed breakdown
        $statusCounts = $allItems->groupBy('status')->map->count();

        // ─── PAGINATE ─────────────────────────────────────────────────────────────
        $paginatedItems = $allItems->forPage($currentPage, $perPage);
        $closedCalls = new LengthAwarePaginator(
            $paginatedItems,
            $totalCount,
            $perPage,
            $currentPage,
            ['path' => $path, 'pageName' => 'page']
        );

        // ─── ALL STATUSES FOR DROPDOWN ────────────────────────────────────────────
        // Pull from the full unfiltered records for the current client
        // so the dropdown always shows all possible options
        $allStatuses = $allItems->pluck('status')->filter()->unique()->sort()->values();

        // ─── AJAX ─────────────────────────────────────────────────────────────────
        if ($request->ajax()) {
            return response()->json([
                'table_body'       => view('closer-section.client-view-cards', compact('closedCalls'))->render(),
                'pagination_links' => $closedCalls->appends([
                    'per_page'      => $perPage,
                    'search'        => $search,
                    'status_filter' => $statusFilter,
                ])->links('pagination::bootstrap-5')->toHtml(),
            ]);
        }

        return view('closer-section.client-view', [
            'closedCalls'    => $closedCalls,
            'perPage'        => $perPage,
            'search'         => $search,
            'statusFilter'   => $statusFilter,
            'allStatuses'    => $allStatuses,
            // Pass pre-computed counts to the view
            'totalCount'     => $totalCount,
            'approvedCount'  => $approvedCount,
            'pendingCount'   => $pendingCount,
            'rejectedCount'  => $rejectedCount,
            'statusCounts'   => $statusCounts,   // ['Funded' => 3, 'Pending' => 5, ...]
        ]);
    }

public function salesagentshow(Request $request)
{
    // 🔒 Check permission first
    if (!auth()->user()->can('agent sales')) {
        // Handle AJAX requests differently
        if ($request->ajax()) {
            return response()->json([
                'error' => 'You don\'t have permission to view this page.',
                'redirect' => route('dashboard') // or any other route
            ], 403);
        }

        // Option 1: Abort with 403 error
        abort(403, 'You don\'t have permission to view this page.');

        
    }

    $perPage = $request->get('per_page', 50);
    $search = $request->get('search');
    $startDate = $request->get('start_date');
    $endDate = $request->get('end_date');

    // Build the query
    $query = ClosedCall::query();

    // Apply search filter if provided
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

    // Apply date range filter if provided
    if ($startDate && $endDate) {
        try {
            $startDate = Carbon::createFromFormat('Y-m-d', $startDate)->startOfDay();
            $endDate = Carbon::createFromFormat('Y-m-d', $endDate)->endOfDay();
            $query->whereBetween('created_at', [$startDate, $endDate]);
        } catch (Exception $e) {
            Log::error('Invalid date format in salesagentshow: ' . $e->getMessage());
        }
    } elseif ($startDate) {
        try {
            $startDate = Carbon::createFromFormat('Y-m-d', $startDate)->startOfDay();
            $query->where('created_at', '>=', $startDate);
        } catch (Exception $e) {
            Log::error('Invalid start date format: ' . $e->getMessage());
        }
    } elseif ($endDate) {
        try {
            $endDate = Carbon::createFromFormat('Y-m-d', $endDate)->endOfDay();
            $query->where('created_at', '<=', $endDate);
        } catch (Exception $e) {
            Log::error('Invalid end date format: ' . $e->getMessage());
        }
    }

    $allowedStatuses = ['Sale made'];

    // Get paginated results
    $closedCalls = $query->whereIn('agent_status', $allowedStatuses)
                        ->orderBy('created_at', 'desc')
                        ->paginate($perPage);

    // Append query parameters to pagination links
    $closedCalls->appends($request->all());

    $currentMonth = Carbon::now()->startOfMonth();
    $pendingCount = ClosedCall::where('status', 'pending')
        ->where('created_at', '>=', $currentMonth)
        ->count();

    return view('closer-section.salesagentshow', compact('closedCalls', 'pendingCount'));
}

public function salesagentshowforteamlead(Request $request)
{
    $user = auth()->user();
    $normalizedType = strtolower(trim((string) $user->type));
    $isTeamLead = in_array($normalizedType, ['team lead', 'teamlead'], true);

    // 🔒 Check permission first
    if (!$user->can('agent sales for teamlead') && !$isTeamLead) {
        // Handle AJAX requests differently
        if ($request->ajax()) {
            return response()->json([
                'error' => 'You don\'t have permission to view this page.',
                'redirect' => route('dashboard')
            ], 403);
        }

        // Abort with 403 error
        abort(403, 'You don\'t have permission to view this page.');
    }

    $perPage = $request->get('per_page', 50);
    $search = $request->get('search');
    $startDate = $request->get('start_date');
    $endDate = $request->get('end_date');

    // Build the query with only required columns
    $query = ClosedCall::query();
    if ($isTeamLead) {
        // Team leads should see records across both centers on this page.
        $query->withoutGlobalScope('center');
    }

    $query->select([
        'id',
        'created_at',
        'closername',
        'agentname',
        'status',
        'center_name',
        'junior_closer_name',
        'closer_id', // Needed for juniorcloser relationship
        'teamname',
        'lead_id',
        'dialer_name_new'
    ]);

    // Apply search filter if provided
    if ($search) {
        $query->where(function($q) use ($search) {
            $q->where('closername', 'LIKE', "%{$search}%")
              ->orWhere('agentname', 'LIKE', "%{$search}%")
              ->orWhere('status', 'LIKE', "%{$search}%")
              ->orWhere('center_name', 'LIKE', "%{$search}%")
              ->orWhere('junior_closer_name', 'LIKE', "%{$search}%")
              ->orWhere('teamname', 'LIKE', "%{$search}%")
              ->orWhere('lead_id', 'LIKE', "%{$search}%")
              ->orWhere('dialer_name_new', 'LIKE', "%{$search}%")
              ->orWhere('id', 'LIKE', "%{$search}%");
        });
    }

    // Apply date range filter if provided
    if ($startDate && $endDate) {
        try {
            $startDate = Carbon::createFromFormat('Y-m-d', $startDate)->startOfDay();
            $endDate = Carbon::createFromFormat('Y-m-d', $endDate)->endOfDay();
            $query->whereBetween('created_at', [$startDate, $endDate]);
        } catch (Exception $e) {
            Log::error('Invalid date format in salesagentshowforteamlead: ' . $e->getMessage());
        }
    } elseif ($startDate) {
        try {
            $startDate = Carbon::createFromFormat('Y-m-d', $startDate)->startOfDay();
            $query->where('created_at', '>=', $startDate);
        } catch (Exception $e) {
            Log::error('Invalid start date format: ' . $e->getMessage());
        }
    } elseif ($endDate) {
        try {
            $endDate = Carbon::createFromFormat('Y-m-d', $endDate)->endOfDay();
            $query->where('created_at', '<=', $endDate);
        } catch (Exception $e) {
            Log::error('Invalid end date format: ' . $e->getMessage());
        }
    }

    $allowedStatuses = ['Sale made'];

    // Get paginated results
    $closedCalls = $query->whereIn('agent_status', $allowedStatuses)
                        ->with('juniorcloser:id,name') // Only load id and name from relationship
                        ->orderBy('created_at', 'desc')
                        ->paginate($perPage);

    // Append query parameters to pagination links
    $closedCalls->appends($request->all());

    $currentMonth = Carbon::now()->startOfMonth();
    $pendingQuery = ClosedCall::query();
    if ($isTeamLead) {
        $pendingQuery->withoutGlobalScope('center');
    }

    $pendingCount = $pendingQuery->where('status', 'pending')
        ->where('created_at', '>=', $currentMonth)
        ->count();

    return view('closer-section.salesagentshowforteam', compact('closedCalls', 'pendingCount'));
}


public function exportSalesAgentData(Request $request)
{
    if (!auth()->user()->can('agent sales')) {
        abort(403);
    }

    // Prevent timeouts and memory exhaustion for long date ranges (e.g. 6–8 months)
    set_time_limit(600);
    if (function_exists('ini_set')) {
        @ini_set('memory_limit', '512M');
    }

    $search = $request->get('search');
    $startDate = $request->get('start_date');
    $endDate = $request->get('end_date');

    // Build the query (same as in salesagentshow)
    $query = ClosedCall::query();

    // Apply search filter if provided
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

    // Apply date range filter if provided
    $startDateObj = null;
    $endDateObj = null;
    if ($startDate && $endDate) {
        try {
            $startDateObj = Carbon::createFromFormat('Y-m-d', $startDate)->startOfDay();
            $endDateObj = Carbon::createFromFormat('Y-m-d', $endDate)->endOfDay();
            $query->whereBetween('created_at', [$startDateObj, $endDateObj]);
        } catch (\Exception $e) {
            Log::error('Invalid date format in export: ' . $e->getMessage());
        }
    } elseif ($startDate) {
        try {
            $startDateObj = Carbon::createFromFormat('Y-m-d', $startDate)->startOfDay();
            $query->where('created_at', '>=', $startDateObj);
        } catch (\Exception $e) {
            Log::error('Invalid start date format: ' . $e->getMessage());
        }
    } elseif ($endDate) {
        try {
            $endDateObj = Carbon::createFromFormat('Y-m-d', $endDate)->endOfDay();
            $query->where('created_at', '<=', $endDateObj);
        } catch (\Exception $e) {
            Log::error('Invalid end date format: ' . $e->getMessage());
        }
    }

    $allowedStatuses = ['Sale made'];
    $query->whereIn('agent_status', $allowedStatuses);

    // Cheap count for header (avoids loading all rows)
    $totalCount = (clone $query)->count();

    // Create filename with current date and filters
    $filename = 'sales_agent_data_' . date('Y_m_d_H_i_s');
    if ($startDateObj && $endDateObj) {
        $filename .= '_' . $startDateObj->format('Y_m_d') . '_to_' . $endDateObj->format('Y_m_d');
    }
    if ($search) {
        $filename .= '_filtered';
    }
    $filename .= '.csv';

    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => "attachment; filename=\"$filename\"",
    ];

    $callback = function() use ($query, $totalCount, $search, $startDateObj, $endDateObj) {
        $file = fopen('php://output', 'w');
        
        // Add export info header
        fputcsv($file, ['Sales Agent Data Export']);
        fputcsv($file, ['Generated: ' . now()->format('Y-m-d H:i:s')]);
        fputcsv($file, ['Total Records: ' . $totalCount]);
        
        if ($search) {
            fputcsv($file, ['Search Filter: ' . $search]);
        }
        
        if ($startDateObj && $endDateObj) {
            fputcsv($file, ['Date Range: ' . $startDateObj->format('Y-m-d') . ' to ' . $endDateObj->format('Y-m-d')]);
        } elseif ($startDateObj) {
            fputcsv($file, ['Start Date: ' . $startDateObj->format('Y-m-d')]);
        } elseif ($endDateObj) {
            fputcsv($file, ['End Date: ' . $endDateObj->format('Y-m-d')]);
        }
        
        fputcsv($file, []); // Empty row
        
        // Add column headers
        fputcsv($file, [
            'ID',
            'Date (Los Angeles)',
            'Time (Los Angeles)',
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
        
        // Stream rows in chunks to avoid memory exhaustion and timeouts
        $query->with(['client', 'closer', 'juniorcloser'])
            ->orderBy('created_at', 'desc')
            ->chunk(500, function ($closedCalls) use ($file) {
                foreach ($closedCalls as $call) {
                    $createdAt = $call->created_at->setTimezone('America/Los_Angeles');

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
                        $call->closername ?? optional($call->closer)->name ?? 'N/A',
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
            });
        
        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}


public function exportSalesAgentDatateam(Request $request)
{
    $user = auth()->user();
    $isTeamLead = in_array(strtolower((string) $user->type), ['team lead', 'teamlead'], true);

    if (!$user->can('agent sales') && !$user->can('agent sales for teamlead') && !$isTeamLead) {
        abort(403);
    }

    // Prevent timeouts and memory exhaustion for long date ranges (e.g. 6–8 months)
    set_time_limit(600);
    if (function_exists('ini_set')) {
        @ini_set('memory_limit', '512M');
    }

    $search = $request->get('search');
    $startDate = $request->get('start_date');
    $endDate = $request->get('end_date');

    // Build the query (same as in salesagentshow)
    $query = ClosedCall::query();
    if ($isTeamLead) {
        // Team leads should export records across both centers from team view.
        $query->withoutGlobalScope('center');
    }

    // Apply search filter if provided
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

    // Apply date range filter if provided
    $startDateObj = null;
    $endDateObj = null;
    if ($startDate && $endDate) {
        try {
            $startDateObj = Carbon::createFromFormat('Y-m-d', $startDate)->startOfDay();
            $endDateObj = Carbon::createFromFormat('Y-m-d', $endDate)->endOfDay();
            $query->whereBetween('created_at', [$startDateObj, $endDateObj]);
        } catch (\Exception $e) {
            Log::error('Invalid date format in export: ' . $e->getMessage());
        }
    } elseif ($startDate) {
        try {
            $startDateObj = Carbon::createFromFormat('Y-m-d', $startDate)->startOfDay();
            $query->where('created_at', '>=', $startDateObj);
        } catch (\Exception $e) {
            Log::error('Invalid start date format: ' . $e->getMessage());
        }
    } elseif ($endDate) {
        try {
            $endDateObj = Carbon::createFromFormat('Y-m-d', $endDate)->endOfDay();
            $query->where('created_at', '<=', $endDateObj);
        } catch (\Exception $e) {
            Log::error('Invalid end date format: ' . $e->getMessage());
        }
    }

    $allowedStatuses = ['Sale made'];
    $query->whereIn('agent_status', $allowedStatuses);

    // Cheap count for header (avoids loading all rows)
    $totalCount = (clone $query)->count();

    // Create filename with current date and filters
    $filename = 'sales_agent_data_' . date('Y_m_d_H_i_s');
    if ($startDateObj && $endDateObj) {
        $filename .= '_' . $startDateObj->format('Y_m_d') . '_to_' . $endDateObj->format('Y_m_d');
    }
    if ($search) {
        $filename .= '_filtered';
    }
    $filename .= '.csv';

    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => "attachment; filename=\"$filename\"",
    ];

    $callback = function() use ($query, $totalCount, $search, $startDateObj, $endDateObj) {
        $file = fopen('php://output', 'w');
        
        // Add export info header
        fputcsv($file, ['Sales Agent Data Export']);
        fputcsv($file, ['Generated: ' . now()->format('Y-m-d H:i:s')]);
        fputcsv($file, ['Total Records: ' . $totalCount]);
        
        if ($search) {
            fputcsv($file, ['Search Filter: ' . $search]);
        }
        
        if ($startDateObj && $endDateObj) {
            fputcsv($file, ['Date Range: ' . $startDateObj->format('Y-m-d') . ' to ' . $endDateObj->format('Y-m-d')]);
        } elseif ($startDateObj) {
            fputcsv($file, ['Start Date: ' . $startDateObj->format('Y-m-d')]);
        } elseif ($endDateObj) {
            fputcsv($file, ['End Date: ' . $endDateObj->format('Y-m-d')]);
        }
        
        fputcsv($file, []); // Empty row
        
        // Add column headers
        fputcsv($file, [
            'ID',
            'Date (Los Angeles)',
            'Time (Los Angeles)',
            'Closer Name',
            'Status',
            'Center Name',
            'Junior Closer',
            'Team Name',
            'Agent Name',
            'Lead ID',
        ]);
        
        // Stream rows in chunks to avoid memory exhaustion and timeouts
        $query->with(['closer', 'juniorcloser'])
            ->orderBy('created_at', 'desc')
            ->chunk(500, function ($closedCalls) use ($file) {
                foreach ($closedCalls as $call) {
                    $createdAt = $call->created_at->setTimezone('America/Los_Angeles');
                    fputcsv($file, [
                        $call->id,
                        $createdAt->format('Y-m-d'),
                        $createdAt->format('H:i:s'),
                        $call->closername ?? optional($call->closer)->name ?? 'N/A',
                        $call->status ?? 'N/A',
                        $call->center_name ?? 'N/A',
                        $call->juniorcloser->name ?? $call->junior_closer_name ?? 'N/A',
                        $call->teamname ?? 'N/A',
                        $call->agentname ?? 'N/A',
                        $call->lead_id ?? 'N/A',
                    ]);
                }
            });
        
        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}

public function index(Request $request)
{
    // Check if user has permission to view closed calls.
    // Allow platform-admin types even if the permission isn't assigned in Spatie.
    $authUser = auth()->user();
    $bypassTypes = ['company', 'super admin'];
    if (!$authUser->can('closed call records') && !in_array($authUser->type, $bypassTypes, true)) {
        // Handle AJAX requests differently
        if ($request->ajax()) {
            return response()->json([
                'error' => 'You don\'t have permission to view this page.',
                'redirect' => route('dashboard') // or any other route you want to redirect to
            ], 403);
        }
        
        // For regular requests, you can either:
        // Option 1: Abort with 403 error
        abort(403, 'You don\'t have permission to view this page.');
        
        // Option 2: Redirect back with error message (uncomment if you prefer this)
        // return redirect()->back()->with('error', 'You don\'t have permission to view this page.');
        
        // Option 3: Redirect to dashboard with error (uncomment if you prefer this)
        // return redirect()->route('dashboard')->with('error', 'You don\'t have permission to view this page.');
    }

    $perPage = $request->input('per_page', 5); // Default to 5 records per page
    $search = $request->input('search');

    $query = ClosedCall::query()->orderBy('created_at', 'desc'); // Added this line

    // Check user type and filter records accordingly
    if (auth()->user()->type === 'client') {
        // Step 1: Get the authenticated user's email
        $authUserEmail = auth()->user()->email;
        
        // Step 2: Try to find the client record in clients table using email
        $client = \App\Models\Client::where('email', $authUserEmail)->first();
        
        if ($client) {
            // PARENT CLIENT LOGIC
            // Step 3: Get the client ID from clients table
            $clientId = $client->id;
            
            // Step 4: Find all users with type 'client' and client_id matching the client ID
            $associatedUserIds = \App\Models\User::where('type', 'client')
                ->where('client_id', $clientId)
                ->pluck('id')
                ->toArray();
            
            // Step 5: Filter ClosedCall records using the associated user IDs
            if (!empty($associatedUserIds)) {
                $query->whereIn('clients_id', $associatedUserIds);
            } else {
                // If no associated users found, return empty result
                $query->where('id', 0); // This will return no results
            }
        } else {
            // CHILD CLIENT LOGIC
            // Email not found in clients table, so this is a child client
            // Use the user's ID directly to match with clients_id in closed_calls
            $query->where('clients_id', auth()->user()->id);
        }
    }
    // For other user types, show all records (no additional filtering needed)

    // Apply search if a search term is entered
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
    
    // Handle AJAX requests
    if ($request->ajax()) {
        return response()->json([
            'table_body' => view('closer-section.table-body', compact('closedCalls'))->render(),
            'pagination_links' => $closedCalls->appends(['per_page' => $perPage, 'search' => $search])->links('pagination::bootstrap-5')->toHtml()
        ]);
    }

    return view('closer-section.index', [
        'closedCalls' => $closedCalls,
        'perPage' => $perPage,
        'search' => $search
    ]);
}


    




public function closerview(){
        $userId = auth()->user()->id; // Get the logged-in user's ID
        $employee = Auth::user();
        $dialer_id = $employee->dialer_id;

        // Only show records where the closer_id matches the current user's ID
        $closedCalls = ClosedCall::where('closer_id', $userId)->paginate(25);
    
        $currentMonth = Carbon::now()->startOfMonth();
        $pendingCount = ClosedCall::where('status', 'pending')
            ->where('created_at', '>=', $currentMonth)
            ->count();
    
        return view('closer-section.closerview', compact('closedCalls', 'pendingCount','dialer_id'));
}
    
    

    public function clientview()
    {
        // Get the logged-in user's name
        $userName = auth()->user()->name;
    
        // Extract the first part of the name (e.g., 'S4' from 'S4 - Chris Martin')
        $underwriterPrefix = explode(' ', $userName)[0];
    
        // Fetch the closed calls where the underwritername starts with the extracted prefix
        $closedCalls = ClosedCall::where('underwriter_name', 'like', "$underwriterPrefix%")
            ->paginate(5); // Display 5 records per page
    
        // Count pending calls for the current month
        $currentMonth = Carbon::now()->startOfMonth();
        $pendingCount = ClosedCall::where('status', 'pending')
            ->where('created_at', '>=', $currentMonth)
            ->count();
    
        return view('closer-section.clientview', compact('closedCalls', 'pendingCount'));
    }
    
    // all closers stats reports starts here
    public function closers_stats()
    {
        $currentMonth = Carbon::now()->startOfMonth();
        $pendingCount = ClosedCall::where('status', 'pending')
            ->where('created_at', '>=', $currentMonth)
            ->count();
        $approvedCount = ClosedCall::where('status', 'approved')
            ->where('created_at', '>=', $currentMonth)
            ->count();
        $rejectedCount = ClosedCall::where('status', 'rejected')
            ->where('created_at', '>=', $currentMonth)
            ->count();
        $fundedCount = ClosedCall::where('status', 'funded')
            ->where('created_at', '>=', $currentMonth)
            ->count();
        $chargedbackedCount = ClosedCall::where('status', 'charged_backed')
            ->where('created_at', '>=', $currentMonth)
            ->count();
        $clientsCountMissing = ClosedCall::where(function ($query) {
            $query->whereNull('clients_id');
        })
            ->count();


        $clientsCount = ClosedCall::where(function ($query) {
            $query->WhereNotNull('clients_id');
        })
            ->count();


        // this is leaderborad object
        $threeHoursLater = Carbon::today()->addHours(0);
        $usersWithTotalClosedCalls = User::where('type', 'closer')
            ->withCount([
                'closedCalls' => function ($query) use ($threeHoursLater) {
                    $query->where('created_at', '>=', $threeHoursLater);
                }
            ])
            ->get();




        // center stats starts here

        $currentMonth = Carbon::now()->month;

        $totalJsonsCount = ClosedCall::where('center_name', 'jsons')

            ->whereMonth('created_at', $currentMonth)
            ->count();
        $totalJsonsApproved = ClosedCall::where('center_name', 'jsons')
            ->where('status', 'approved')
            ->whereMonth('created_at', $currentMonth)
            ->count();
        $totalJsonsRejected = ClosedCall::where('center_name', 'jsons')
            ->where('status', 'rejected')
            ->whereMonth('created_at', $currentMonth)
            ->count();
        $totalJsonsChargedbacked = ClosedCall::where('center_name', 'jsons')
            ->where('status', 'charged_backed')
            ->whereMonth('created_at', $currentMonth)
            ->count();
        $totalSellersCount = ClosedCall::where('center_name', 'sellerz')

            ->whereMonth('created_at', $currentMonth)
            ->count();
        $totalSellerzApproved = ClosedCall::where('center_name', 'sellerz')
            ->where('status', 'approved')
            ->whereMonth('created_at', $currentMonth)
            ->count();
        $totalSellerzrejected = ClosedCall::where('center_name', 'sellerz')
            ->where('status', 'rejected')
            ->whereMonth('created_at', $currentMonth)
            ->count();
        $totalSellerzChargedbacked = ClosedCall::where('center_name', 'sellerz')
            ->where('status', 'charged_backed')
            ->whereMonth('created_at', $currentMonth)
            ->count();


        // center stats ends here


        // all the stats for closers here 


        $currentMonth = Carbon::now()->startOfMonth();

        // Retrieve the monthly statistics for each user
        $userStats = ClosedCall::select('closer_id')
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
            // Add more cases for other statuses if needed
            ->where('created_at', '>=', $currentMonth)
            ->groupBy('closer_id')
            ->get();



        return view(
            'closer-section.stats',
            compact(
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
                'totalJsonsChargedbacked',

            )
        );
    }
    // all closers stats reports ends here

    public function edit(Request $request,$id)
    {
            // Check if user has permission to view closed calls
        $authUser = auth()->user();
        $bypassTypes = ['company', 'super admin'];
        if (!$authUser->can('closed call records') && !in_array($authUser->type, $bypassTypes, true)) {
            // Handle AJAX requests differently
            if ($request->ajax()) {
                return response()->json([
                    'error' => 'You don\'t have permission to view this page.',
                    'redirect' => route('dashboard') // or any other route you want to redirect to
                ], 403);
            }
            
            // For regular requests, you can either:
            // Option 1: Abort with 403 error
            abort(403, 'You don\'t have permission to view this page.');
            
        }
        $update = ClosedCall::find($id);
        $closers = User::whereIn('type', ['closer', 'Project Manager'])->orderBy('name', 'asc')->get();

        $clients = Client::all(); // Get all clients from the Client table
        $users = User::where('type', 'client')->get(); // Get all users from the User table
    
        // Pass clients and users to the view
        return view('closer-section.editform', [
            'update' => $update, 
            'clients' => $clients, 
            'closers' => $closers,
            'users' => $users // Pass all users to the view
        ]);
    }

    public function getUsers($clientId)
    {
        // 🔒 Authorization check - prevent user enumeration
        $user = auth()->user();
        
        if (!$user) {
            Log::warning('Unauthorized getUsers attempt - no user', [
                'client_id' => $clientId,
                'ip' => request()->ip()
            ]);
            return response()->json(['error' => 'Unauthorized access.'], 403);
        }

        // Check if user has permission to view users
        $hasPermission = Gate::forUser($user)->allows('view users') || 
                        Gate::forUser($user)->allows('manage users') ||
                        in_array($user->type, ['company', 'super admin', 'admin', 'Director', 'Project Manager'], true);
        
        if (!$hasPermission) {
            Log::warning('Unauthorized getUsers attempt - no permission', [
                'user_id' => $user->id,
                'user_type' => $user->type,
                'client_id' => $clientId,
                'ip' => request()->ip()
            ]);
            return response()->json(['error' => 'You do not have permission to view users.'], 403);
        }

        // Validate client_id is numeric to prevent injection
        if (!is_numeric($clientId)) {
            Log::warning('Invalid client_id in getUsers', [
                'user_id' => $user->id,
                'client_id' => $clientId,
                'ip' => request()->ip()
            ]);
            return response()->json(['error' => 'Invalid client ID.'], 400);
        }

        // 🔒 Verify user has access to this specific client
        $hasAccess = false;
        
        if (in_array($user->type, ['company', 'super admin', 'admin', 'Director', 'Project Manager'], true)) {
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
            Log::warning('Unauthorized getUsers attempt - no access to client', [
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
        Log::info('Users retrieved by client_id', [
            'user_id' => $user->id,
            'user_type' => $user->type,
            'requested_client_id' => $clientId,
            'result_count' => $users->count(),
            'ip' => request()->ip()
        ]);
        
        return response()->json($users);
    }
    
    // ... (Your edit and update methods remain the same)


public function update(Request $request, $id)
{
    try {
        // 🟩 Step 0: Authorization check
        $authUser = auth()->user();
        $bypassTypes = ['company', 'super admin'];
        if (!$authUser->can('closed call records') && !in_array($authUser->type, $bypassTypes, true)) {
            if ($request->ajax()) {
                return response()->json([
                    'error' => 'You don\'t have permission to perform this action.',
                    'redirect' => route('dashboard')
                ], 403);
            }
            abort(403, 'You don\'t have permission to perform this action.');
        }

        $update = ClosedCall::find($id);
        
        if (!$update) {
            return redirect()->back()->with('error', 'Record not found.');
        }

        // 🟩 Step 1: Validate incoming data
        $validated = $request->validate([
            'customer_full_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'alternate_phone_number' => 'nullable|string|max:20',
            'cx_email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:20',
            'gender' => 'nullable|string|max:10',
            'martial_status' => 'nullable|string|max:50',
            'age' => 'nullable|integer|min:0',
            'dob' => 'nullable|date',
            'palce_of_birth' => 'nullable|string|max:255',
            'height' => 'nullable|string|max:20',
            'weight' => 'nullable|string|max:20',
            'closername' => 'nullable|string|max:255',
            'social_security' => 'nullable|string|max:50',
            'smoker' => 'nullable|string|max:10',
            'health_condition' => 'nullable|string',
            'medication' => 'nullable|string',
            'hospital_name' => 'nullable|string|max:255',
            'hospital_address' => 'nullable|string|max:255',
            'physician_name' => 'nullable|string|max:255',
            'monthly_premium' => 'nullable|numeric|min:0',
            'carrier' => 'nullable|string|max:255',
            'coverage_plan' => 'nullable|string|max:255',
            'customer_eligibility' => 'nullable|string|max:255',
            'beneficiary' => 'nullable|string|max:255',
            'beneficiary_relation' => 'nullable|string|max:100',
            'beneficiary_phone' => 'nullable|string|max:20',
            'beneficiary_dob' => 'nullable|date',
            'payor' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'bank_address' => 'nullable|string|max:255',
            'routing_number' => 'nullable|string|max:50',
            'bank_account_number' => 'nullable|string|max:50',
            'debit_card_direct_express_no' => 'nullable|string|max:50',
            'debit_card_direct_express_expiration' => 'nullable|string|max:10',
            'debit_card_direct_express_cvv' => 'nullable|string|max:10',
            'account_type' => 'nullable|string|max:50',
            'initial_draft_date' => 'nullable|date',
            'future_draft_date' => 'nullable|date',
            'underwriter_name' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
            'junior_closer_name' => 'nullable|string|max:255',
            'juniorcloser2' => 'nullable|string|max:255',
            'center_name' => 'nullable|string|max:255',
            'sale_made_by' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:100',
            'clients_comment' => 'nullable|string',
            'dialername' => 'nullable|string|max:255',
            'dialeragentname' => 'nullable|string|max:255',
            'lead_id' => 'nullable|string|max:255',
            'list_id_1' => 'nullable|string|max:255',
            'list_id_2' => 'nullable|string|max:255',
            'clients_id' => 'nullable|integer',
            'closer_id' => 'nullable|integer',
        ]);

        // 🟩 Step 2: Validate bank or card details requirement
        $hasBankDetails = $request->filled(['bank_name', 'routing_number', 'bank_account_number']);
        $hasDebitCardDetails = $request->filled(['debit_card_direct_express_no', 'debit_card_direct_express_expiration', 'debit_card_direct_express_cvv']);
        
        if (!$hasBankDetails && !$hasDebitCardDetails) {
            return back()->withErrors([
                'bank_or_card' => 'You must provide either bank details OR debit card details.',
            ])->withInput();
        }

        // 🟩 Step 3: Check for duplicates (only if "allow_duplicate" is NOT checked)
        if (!$request->has('allow_duplicate')) {
            $existingRecord = ClosedCall::where('id', '!=', $id)
                ->where(function($query) use ($validated) {
                    $query->where('phone_number', $validated['phone_number'])
                          ->orWhere('alternate_phone_number', $validated['phone_number']);
                    
                    if (!empty($validated['alternate_phone_number'])) {
                        $query->orWhere('phone_number', $validated['alternate_phone_number'])
                              ->orWhere('alternate_phone_number', $validated['alternate_phone_number']);
                    }
                })->first();

            if ($existingRecord) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Record already exists!')
                    ->with('existing_record', $existingRecord)
                    ->with('show_duplicate_warning', true);
            }
        }

        // Helper: only update fields that actually have a value in payload.
        $setIfFilled = function (string $field, ?string $column = null) use ($request, $validated, $update) {
            if ($request->filled($field) && array_key_exists($field, $validated)) {
                $update->{$column ?? $field} = $validated[$field];
            }
        };

        // 🟩 Track CLIENT assignment history when client changes (only if provided in payload)
        if ($request->filled('clients_id') && array_key_exists('clients_id', $validated)) {
            $oldClientId = $update->clients_id;
            $newClientId = $validated['clients_id'];

            if ($oldClientId != $newClientId) {
                $currentUser = auth()->user()->name;
                $timestamp = now()->format('d-M-Y h:i A');

                $oldClientName = $oldClientId ? \App\Models\User::find($oldClientId)?->name : 'None';
                $newClientName = $newClientId ? \App\Models\User::find($newClientId)?->name : 'None';

                $historyEntry = "Assigned by {$currentUser} on {$timestamp} - Changed from '{$oldClientName}' to '{$newClientName}'\n";
                $update->assigned_history = ($update->assigned_history ?? '') . $historyEntry;
            }
        }

        // 🟩 Track CLOSER assignment history when closer changes (only if provided in payload)
        if ($request->filled('closername') && array_key_exists('closername', $validated)) {
            $oldCloserName = $update->closername;
            $newCloserName = $validated['closername'];

            if ($oldCloserName != $newCloserName) {
                $currentUser = auth()->user()->name;
                $timestamp = now()->format('d-M-Y h:i A');

                $historyEntry = "Assigned by {$currentUser} on {$timestamp} - Changed from '{$oldCloserName}' to '{$newCloserName}'\n";
                $update->closer_assigned_history = ($update->closer_assigned_history ?? '') . $historyEntry;
            }
        }

        // 🟩 Step 4: Update only filled fields from payload
        $setIfFilled('customer_full_name');
        $setIfFilled('phone_number');
        $setIfFilled('alternate_phone_number');
        $setIfFilled('cx_email');
        $setIfFilled('address');
        $setIfFilled('city');
        $setIfFilled('state');
        $setIfFilled('zip_code');
        $setIfFilled('gender');
        $setIfFilled('martial_status');
        $setIfFilled('age');

        if ($request->filled('dob') && !empty($validated['dob'])) {
            $update->dob = date('Y-m-d', strtotime($validated['dob']));
        }

        $setIfFilled('social_security');
        $setIfFilled('smoker');
        $setIfFilled('health_condition');
        $setIfFilled('medication');
        $setIfFilled('hospital_name');
        $setIfFilled('hospital_address');
        $setIfFilled('physician_name');

        $setIfFilled('customer_eligibility');
        $setIfFilled('beneficiary');
        $setIfFilled('beneficiary_relation');
        $setIfFilled('beneficiary_phone');

        if ($request->filled('beneficiary_dob') && !empty($validated['beneficiary_dob'])) {
            $update->beneficiary_dob = date('Y-m-d', strtotime($validated['beneficiary_dob']));
        }

        $setIfFilled('payor');
        $setIfFilled('bank_name');
        $setIfFilled('bank_address');
        $setIfFilled('routing_number');
        $setIfFilled('bank_account_number');
        $setIfFilled('debit_card_direct_express_no');

        if ($request->filled('debit_card_direct_express_expiration') && !empty($validated['debit_card_direct_express_expiration'])) {
            $update->debit_card_direct_express_expiration = date('Y-m-d', strtotime($validated['debit_card_direct_express_expiration']));
        }

        $setIfFilled('debit_card_direct_express_cvv');
        $setIfFilled('account_type');

        if ($request->filled('initial_draft_date') && !empty($validated['initial_draft_date'])) {
            $update->initial_draft_date = date('Y-m-d', strtotime($validated['initial_draft_date']));
        }

        if ($request->filled('future_draft_date') && !empty($validated['future_draft_date'])) {
            $update->future_draft_date = date('Y-m-d', strtotime($validated['future_draft_date']));
        }

        $setIfFilled('underwriter_name');
        $setIfFilled('remarks');
        $setIfFilled('closer_id');
        $setIfFilled('junior_closer_name');
        $setIfFilled('center_name');
        $setIfFilled('sale_made_by');
        $setIfFilled('status');
        $setIfFilled('clients_comment');
        $setIfFilled('dialername');
        $setIfFilled('dialeragentname');
        $setIfFilled('lead_id');
        $setIfFilled('closername');
        $setIfFilled('juniorcloser2');
        $setIfFilled('list_id_1');
        $setIfFilled('list_id_2');
        $setIfFilled('monthly_premium');
        $setIfFilled('coverage_plan');
        $setIfFilled('carrier');
        $setIfFilled('clients_id');

        $update->save();

        // return redirect()->route('closed_calls.index')->with('success', 'Policy updated successfully.');
        
        return redirect()->route('closed-calls.show', $id)
    ->with('success', 'Policy updated successfully.');
    } catch (\Illuminate\Validation\ValidationException $e) {
        return redirect()->back()->withErrors($e->errors())->withInput();
    } catch (\Exception $e) {
        return redirect()->back()->withInput()->with('error', 'Error: ' . $e->getMessage());
    }
}


public function show($id)
{
    // Get the authenticated user
    $user = auth()->user();
    
    // If user is a client, proceed to show the page
    if ($user->type === 'client') {
        $closedCall = ClosedCall::findOrFail($id);
        
        // Authorization check for client-specific record access
        $authUserEmail = $user->email;
        $client = \App\Models\Client::where('email', $authUserEmail)->first();
        
        if ($client) {
            // PARENT CLIENT LOGIC - check if this record belongs to associated users
            $clientId = $client->id;
            $associatedUserIds = \App\Models\User::where('type', 'client')
                ->where('client_id', $clientId)
                ->pluck('id')
                ->toArray();
            
            if (!in_array($closedCall->clients_id, $associatedUserIds)) {
                abort(404, 'Record not found.');
            }
        } else {
            // CHILD CLIENT LOGIC - check if this record belongs to the user
            if ($closedCall->clients_id !== $user->id) {
                abort(404, 'Record not found.');
            }
        }
        
        return view('closer-section.show', compact('closedCall'));
    }
    
    // For non-client users, check permission
    $bypassTypes = ['company', 'super admin'];
    if (!$user->can('closed call records') && !in_array($user->type, $bypassTypes, true)) {
        abort(403, 'You don\'t have permission to view this page.');
    }
    
    // If permission exists, fetch and show the record
    $closedCall = ClosedCall::findOrFail($id);
    return view('closer-section.show', compact('closedCall'));
}
public function showagentsales(Request $request,$id)
{
    // Check if user has permission to view closed calls
    $authUser = auth()->user();
    $bypassTypes = ['company', 'super admin'];
    if (!$authUser->can('outsource records') && !in_array($authUser->type, $bypassTypes, true)) {
        // Handle AJAX requests differently
        if ($request->ajax()) {
            return response()->json([
                'error' => 'You don\'t have permission to view this page.',
                'redirect' => route('dashboard') // or any other route you want to redirect to
            ], 403);
        }
        
        // For regular requests, you can either:
        // Option 1: Abort with 403 error
        abort(403, 'You don\'t have permission to view this page.');
        
        // Option 2: Redirect back with error message (uncomment if you prefer this)
        // return redirect()->back()->with('error', 'You don\'t have permission to view this page.');
        
        // Option 3: Redirect to dashboard with error (uncomment if you prefer this)
        // return redirect()->route('dashboard')->with('error', 'You don\'t have permission to view this page.');
    }
    $closedCall = ClosedCall::findOrFail($id); // Get the closed call record
    return view('closer-section.agentsales', compact('closedCall')); // Return data to the view
}


    // clients sections starts here 

    // shows clients his policies
    public function clientindex()
    {
        $userName = auth()->user()->name;
    
        // Extract the first part of the name (e.g., 'S4' from 'S4 - Chris Martin')
        $underwriterPrefix = explode(' ', $userName)[0];
    
        // Fetch the closed calls where the underwritername starts with the extracted prefix
        $closedCalls = ClosedCall::where('underwriter_name', 'like', "$underwriterPrefix%")
            ->paginate(5); // Display 5 records per page
    

        $userId = auth()->id();

        // Count total pending closed calls for user type "client" matching with auth
        $pendingCount = ClosedCall::where('clients_id', $userId) // Assuming 'user_id' is the column representing the user in the 'closed_calls' table
            ->where('status', 'pending')
            ->count();
        $approvedCount = ClosedCall::where('clients_id', $userId) // Assuming 'user_id' is the column representing the user in the 'closed_calls' table
            ->where('status', 'approved')
            ->count();
        $rejectedCount = ClosedCall::where('clients_id', $userId) // Assuming 'user_id' is the column representing the user in the 'closed_calls' table
            ->where('status', 'rejected')
            ->count();
        $fundedCount = ClosedCall::where('clients_id', $userId) // Assuming 'user_id' is the column representing the user in the 'closed_calls' table
            ->where('status', 'funded')
            ->count();
        $chargedbackedCount = ClosedCall::where('clients_id', $userId) // Assuming 'user_id' is the column representing the user in the 'closed_calls' table
            ->where('status', 'charged_backed')
            ->count();

        $currentMonth = Carbon::now()->startOfMonth();

        // Count closed calls for "carrier" column for the current month
        $aig = ClosedCall::where(function ($query) {
            $query->where('carrier', 'AIG');
        })
            ->where('created_at', '>=', $currentMonth)
            ->count();
        $muo = ClosedCall::where(function ($query) {
            $query->where('carrier', 'Mutual_of_Omaha');
        })
            ->where('created_at', '>=', $currentMonth)
            ->count();

        return view(
            'clients.manage-policies',
            compact(
                'closedCalls',
                'pendingCount',
                'approvedCount',
                'rejectedCount',
                'fundedCount',
                'chargedbackedCount',
                'aig',
                'muo'
            )
        );
    }


public function editclient(Request $request, $id)
{
    // 🔒 Authorization check
    $update = ClosedCall::find($id);
    
    if (!$update) {
        return redirect()->back()->with('error', 'Record not found.');
    }
    
    $user = auth()->user();
    
    // Check if user is a client and verify record ownership
    if ($user->type === 'client') {
        if ($update->clients_id !== $user->id) {
            // Handle AJAX requests
            if ($request->ajax()) {
                return response()->json([
                    'error' => 'You don\'t have permission to edit this record.',
                    'redirect' => route('dashboard')
                ], 403);
            }
            
            abort(403, 'You don\'t have permission to edit this record.');
        }
    }
    // Optional: Add additional permission checks for other user types
    elseif (!$user->can('edit client records') && !in_array($user->type, ['company', 'super admin'], true)) {
        if ($request->ajax()) {
            return response()->json([
                'error' => 'You don\'t have permission to edit client records.',
                'redirect' => route('dashboard')
            ], 403);
        }
        
        abort(403, 'You don\'t have permission to edit client records.');
    }
    
      
    $closers = User::where('type', 'closer')->orderBy('name', 'asc')->get();
    $clients = User::where('type', 'client')->orderBy('name', 'asc')->get();
    
    return view('clients.edit-policy', [
        'update' => $update, 
        'clients' => $clients, 
        'closers' => $closers
    ]);
}


public function updateclient(Request $request, $id)
{
    try {
        // 🔒 Step 0: Find record and check authorization
        $update = ClosedCall::find($id);
        
        if (!$update) {
            return redirect()->back()->with('error', 'Record not found.');
        }

    $user = auth()->user();

        // Verify record ownership for clients
    if ($user->type === 'client') {
            if ($update->clients_id !== $user->id) {
                if ($request->ajax()) {
                    return response()->json([
                        'error' => 'You don\'t have permission to update this record.',
                        'redirect' => route('dashboard')
                    ], 403);
                }
                
                abort(403, 'You don\'t have permission to update this record.');
            }
        }
        // Optional: Add additional permission checks for other user types
        elseif (!$user->can('edit client records') && !in_array($user->type, ['company', 'super admin'], true)) {
            if ($request->ajax()) {
                return response()->json([
                    'error' => 'You don\'t have permission to update client records.',
                    'redirect' => route('dashboard')
                ], 403);
            }
            
            abort(403, 'You don\'t have permission to update client records.');
        }

        // 🟩 Step 1: Validate incoming data
        $validated = $request->validate([
            'customer_full_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'alternate_phone_number' => 'nullable|string|max:20',
            'cx_email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:20',
            'gender' => 'nullable|string|max:10',
            'martial_status' => 'nullable|string|max:50',
            'age' => 'nullable|integer|min:0',
            'dob' => 'nullable|date',
            'social_security' => 'nullable|string|max:50',
            'smoker' => 'nullable|string|max:10',
            'health_condition' => 'nullable|string',
            'medication' => 'nullable|string',
            'hospital_name' => 'nullable|string|max:255',
            'hospital_address' => 'nullable|string|max:255',
            'physician_name' => 'nullable|string|max:255',
            'monthly_premium' => 'nullable|numeric|min:0',
            'customer_eligibility' => 'nullable|string|max:255',
            'beneficiary' => 'nullable|string|max:255',
            'beneficiary_relation' => 'nullable|string|max:100',
            'beneficiary_phone' => 'nullable|string|max:20',
            'beneficiary_dob' => 'nullable|date',
            'payor' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'bank_address' => 'nullable|string|max:255',
            'routing_number' => 'nullable|string|max:50',
            'bank_account_number' => 'nullable|string|max:50',
            'debit_card_direct_express_no' => 'nullable|string|max:50',
            'debit_card_direct_express_expiration' => 'nullable|string|max:10',
            'debit_card_direct_express_cvv' => 'nullable|string|max:10',
            'account_type' => 'nullable|string|max:50',
            'initial_draft_date' => 'nullable|date',
            'future_draft_date' => 'nullable|date',
            'remarks' => 'nullable|string',
            'status' => 'nullable|string|max:100',
            'clients_comment' => 'nullable|string',
            'recording_id' => 'nullable|string|max:255',
            'hippa_id' => 'nullable|string|max:255',
            'policy_id' => 'nullable|string|max:255',
            'recording_status' => 'nullable|string|max:100',
            'signature_type' => 'nullable|string|max:100',
            'call_id' => 'nullable|string|max:255',
            'carrier' => 'nullable|string|max:255',
            'client_name_2' => 'nullable|string|max:255',
        ]);

        // 🟩 Step 2: Validate bank or card details requirement
        $hasBankDetails = $request->filled(['bank_name', 'routing_number', 'bank_account_number']);
        $hasDebitCardDetails = $request->filled(['debit_card_direct_express_no', 'debit_card_direct_express_expiration', 'debit_card_direct_express_cvv']);
        
        if (!$hasBankDetails && !$hasDebitCardDetails) {
            return back()->withErrors([
                'bank_or_card' => 'You must provide either bank details OR debit card details.',
            ])->withInput();
        }

        // 🟩 Step 3: Update only fields that are filled in payload
        $setIfFilled = function (string $field, ?string $column = null) use ($request, $validated, $update) {
            if ($request->filled($field) && array_key_exists($field, $validated)) {
                $update->{$column ?? $field} = $validated[$field];
            }
        };

        $setIfFilled('customer_full_name');
        $setIfFilled('phone_number');
        $setIfFilled('alternate_phone_number');
        $setIfFilled('cx_email');
        $setIfFilled('address');
        $setIfFilled('city');
        $setIfFilled('state');
        $setIfFilled('zip_code');
        $setIfFilled('gender');
        $setIfFilled('martial_status');
        $setIfFilled('age');

        if ($request->filled('dob') && !empty($validated['dob'])) {
            $update->dob = date('Y-m-d', strtotime($validated['dob']));
        }

        $setIfFilled('social_security');
        $setIfFilled('smoker');
        $setIfFilled('health_condition');
        $setIfFilled('medication');
        $setIfFilled('hospital_name');
        $setIfFilled('hospital_address');
        $setIfFilled('physician_name');
        $setIfFilled('monthly_premium');
        $setIfFilled('customer_eligibility');
        $setIfFilled('beneficiary');
        $setIfFilled('beneficiary_relation');
        $setIfFilled('beneficiary_phone');

        if ($request->filled('beneficiary_dob') && !empty($validated['beneficiary_dob'])) {
            $update->beneficiary_dob = date('Y-m-d', strtotime($validated['beneficiary_dob']));
        }

        $setIfFilled('payor');
        $setIfFilled('bank_name');
        $setIfFilled('bank_address');
        $setIfFilled('routing_number');
        $setIfFilled('bank_account_number');
        $setIfFilled('debit_card_direct_express_no');

        if ($request->filled('debit_card_direct_express_expiration') && !empty($validated['debit_card_direct_express_expiration'])) {
            $update->debit_card_direct_express_expiration = date('Y-m-d', strtotime($validated['debit_card_direct_express_expiration']));
        }

        $setIfFilled('debit_card_direct_express_cvv');
        $setIfFilled('account_type');

        if ($request->filled('initial_draft_date') && !empty($validated['initial_draft_date'])) {
            $update->initial_draft_date = date('Y-m-d', strtotime($validated['initial_draft_date']));
        }

        if ($request->filled('future_draft_date') && !empty($validated['future_draft_date'])) {
            $update->future_draft_date = date('Y-m-d', strtotime($validated['future_draft_date']));
        }

        $setIfFilled('remarks');
        $setIfFilled('status');
        $setIfFilled('clients_comment');
        $setIfFilled('recording_id');
        $setIfFilled('hippa_id');
        $setIfFilled('policy_id');
        $setIfFilled('recording_status');
        $setIfFilled('signature_type');
        $setIfFilled('call_id');
        $setIfFilled('carrier');
        $setIfFilled('client_name_2');

        // If the user is a client, ensure clients_id is set to the user's id
        if ($user->type === 'client') {
            $update->clients_id = $user->id;
        }

        $update->save();

        return back()->with('success', 'Policy updated successfully!');
        
    } catch (\Illuminate\Validation\ValidationException $e) {
        return redirect()->back()->withErrors($e->errors())->withInput();
    } catch (\Exception $e) {
        return redirect()->back()->withInput()->with('error', 'Error: ' . $e->getMessage());
    }
}
    
  // ─── Authorized emails (same list as store()) ───────────────────────────────

public function editOwnClient(Request $request, $id)
{
    $update = ClosedCall::find($id);

    if (!$update) {
        return redirect()->back()->with('error', 'Record not found.');
    }

    $user = auth()->user();

    if (!$user->can('client policy')) {
        if ($request->ajax()) {
            return response()->json([
                'error'    => 'You don\'t have permission to view this page.',
                'redirect' => route('dashboard')
            ], 403);
        }
        abort(403, 'You don\'t have permission to view this page.');
    }

    $closers = User::where('type', 'closer')->orderBy('name', 'asc')->get();
    $clients = User::where('type', 'client')->orderBy('name', 'asc')->get();

    return view('clients.edit-client-policy', [
        'update'  => $update,
        'clients' => $clients,
        'closers' => $closers
    ]);
}
    



// ─── Updated method ──────────────────────────────────────────────────────────

public function updateOwnClient(UpdateOwnClientRequest $request, $id)
{
    try {
        $update = ClosedCall::find($id);
        if (!$update) {
            return redirect()->back()->with('error', 'Record not found.');
        }

        $user = auth()->user();

        if (!$user->can('own client policy edit')) {
            if ($request->ajax()) {
                return response()->json([
                    'error'    => 'You don\'t have permission to update this policy.',
                    'redirect' => route('dashboard')
                ], 403);
            }
            abort(403, 'You don\'t have permission to update this policy.');
        }

        $validated = $request->validated();

        $hasBankDetails      = $request->filled(['bank_name', 'routing_number', 'bank_account_number']);
        $hasDebitCardDetails = $request->filled(['debit_card_direct_express_no', 'debit_card_direct_express_expiration', 'debit_card_direct_express_cvv']);

        if (!$hasBankDetails && !$hasDebitCardDetails) {
            return back()
                ->withErrors(['bank_or_card' => 'You must provide either bank details OR debit card details.'])
                ->withInput();
        }

        $filteredValidated = [];
        foreach ($validated as $key => $value) {
            if ($request->filled($key)) {
                $filteredValidated[$key] = $value;
            }
        }

        $dateKeys = [
            'dob',
            'beneficiary_dob',
            'debit_card_direct_express_expiration',
            'initial_draft_date',
            'future_draft_date',
        ];
        foreach ($dateKeys as $key) {
            if (isset($filteredValidated[$key]) && $filteredValidated[$key] !== '') {
                $filteredValidated[$key] = date('Y-m-d', strtotime($filteredValidated[$key]));
            }
        }

        if ($user->type === 'client') {
            $filteredValidated['clients_id'] = $user->id;
        }

        $filteredValidated['updated_by'] = auth()->id();

        $update->update($filteredValidated);

        return redirect()->route('closed-calls.show', $id)->with('success', 'Policy updated successfully!');

    } catch (\Illuminate\Validation\ValidationException $e) {
        return redirect()->back()->withErrors($e->errors())->withInput();
    } catch (\Exception $e) {
        return redirect()->back()->withInput()->with('error', 'Error: ' . $e->getMessage());
    }
}



    public function callback($id)
{
    try {
        $closedCall = ClosedCall::findOrFail($id);
        $user = Auth::user();
        
        // Validation checks
        if (!$user || !$user->dialer_id) {
            return back()->with('error', 'User not authenticated or dialer ID not found.');
        }
        if (empty($closedCall->phone_number)) {
            return back()->with('error', 'Phone number not available for this call.');
        }
        if (!preg_match('/^\+?\d{10,15}$/', $closedCall->phone_number)) {
            return back()->with('error', 'Invalid phone number format.');
        }
        
        // API parameters
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
        
        // Make the API call server-side
        $client = new \GuzzleHttp\Client();
        $response = $client->get($api_url, [
            'query' => $api_params,
            'http_errors' => false
        ]);
        
        $result = json_decode($response->getBody(), true);
        
        // Log the attempt
        Log::info('Callback initiated', [
            'user_id' => $user->id,
            'closed_call_id' => $id,
            'response' => $result
        ]);
        
        // Return to the previous page with a success message
        return back()->with([
            'success' => 'Call initiated successfully! Check your dialer interface.',
            'phone' => substr($closedCall->phone_number, 0, 3) . '-xxx-' . substr($closedCall->phone_number, -4)
        ]);
        
    } catch (\Exception $e) {
        Log::error('Callback error: ' . $e->getMessage(), [
            'id' => $id,
            'trace' => $e->getTraceAsString()
        ]);
        
        return back()->with('error', 'Error initiating callback: ' . $e->getMessage());
    }
}

public function editdialer(Request $request, $id)
{
    // 🔒 Authorization check
    $authUser = auth()->user();
    if (!$authUser->can('edit dialer records') && !in_array($authUser->type, ['company', 'super admin'], true)) {
        // Handle AJAX requests
        if ($request->ajax()) {
            return response()->json([
                'error' => 'You don\'t have permission to edit dialer records.',
                'redirect' => route('dashboard')
            ], 403);
        }
        
        abort(403, 'You don\'t have permission to edit dialer records.');
    }

        $update = ClosedCall::find($id);
    
    if (!$update) {
        return redirect()->back()->with('error', 'Record not found.');
    }

    // Retrieve users by type with optimized queries
    $dialers = User::where('type', 'Dialer Support')->orderBy('name', 'asc')->get();
    $agents = User::where('type', 'Avatar')->orderBy('name', 'asc')->get();
    $closers = User::where('type', 'closer')->orderBy('name', 'asc')->get();

    // Get team names directly from database - bypasses SoftDeletes
    $teams = DB::table('teams')->orderBy('name', 'asc')->get();

    return view('closer-section.dialeredit', [
        'update' => $update,
        'dialers' => $dialers,
        'agents' => $agents,
        'closers' => $closers,
        'teams' => $teams
    ]);
}

public function updatedialer(Request $request, $id)
{
    try {
        // 🔒 Authorization check
        $authUser = auth()->user();
        if (!$authUser->can('edit dialer records') && !in_array($authUser->type, ['company', 'super admin'], true)) {
            // Handle AJAX requests
            if ($request->ajax()) {
                return response()->json([
                    'error' => 'You don\'t have permission to update dialer records.',
                    'redirect' => route('dashboard')
                ], 403);
            }
            
            abort(403, 'You don\'t have permission to update dialer records.');
        }

        $update = ClosedCall::find($id);
        
        if (!$update) {
            return redirect()->back()->with('error', 'Record not found.');
        }

        // 🟩 Step 1: Validate incoming data
        $validated = $request->validate([
            'dialername' => 'nullable|string|max:255',
            'dialeragentname' => 'nullable|string|max:255',
            'agentname' => 'nullable|string|max:255',
            'teamname' => 'nullable|string|max:255',
            'lead_id' => 'nullable|string|max:255',
            'closername' => 'nullable|string|max:255',
            'juniorcloser2' => 'nullable|string|max:255',
            'list_id_1' => 'nullable|string|max:255',
            'list_id_2' => 'nullable|string|max:255',
            'dialer_name_new' => 'nullable|string|max:255',
        ]);

        // 🟩 Step 2: Update only fields that are filled in payload
        $setIfFilled = function (string $field, ?string $column = null) use ($request, $validated, $update) {
            if ($request->filled($field) && array_key_exists($field, $validated)) {
                $update->{$column ?? $field} = $validated[$field];
            }
        };

        $setIfFilled('dialername');
        $setIfFilled('dialeragentname');
        $setIfFilled('agentname');
        $setIfFilled('teamname');
        $setIfFilled('lead_id');
        $setIfFilled('closername');
        $setIfFilled('juniorcloser2');
        $setIfFilled('list_id_1');
        $setIfFilled('list_id_2');
        $setIfFilled('dialer_name_new');

        // Guardrail: don't allow this flow to keep agentname null.
        // If user didn't provide agentname and record is still null, stop with a clear error.
        if (($update->agentname === null || trim((string) $update->agentname) === '')) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Agent Name is required. Please select an Agent Name before saving.');
        }

        $user = auth()->user();

        // If the user is a client, store the user's id as the client_id
        if ($user->type === 'client') {
            $update->clients_id = $user->id;
        }

        $update->save();

        return redirect()->route('closer.salesagentshow')->with('success', 'Policy updated successfully.');
        
    } catch (\Illuminate\Validation\ValidationException $e) {
        return redirect()->back()->withErrors($e->errors())->withInput();
    } catch (\Exception $e) {
        return redirect()->back()->withInput()->with('error', 'Error: ' . $e->getMessage());
    }
}


public function re_edit(Request $request, $id)
{
    // 🔒 Authorization check
    if (!auth()->user()->can('closer re edit')) {
        // Handle AJAX requests
        if ($request->ajax()) {
            return response()->json([
                'error' => 'You don\'t have permission to re-edit records.',
                'redirect' => route('dashboard')
            ], 403);
        }
        
        abort(403, 'You don\'t have permission to re-edit records.');
    }

    $update = ClosedCall::find($id);
    
    if (!$update) {
        return redirect()->back()->with('error', 'Record not found.');
    }

    $closers = User::whereIn('type', ['closer', 'Project Manager'])->orderBy('name', 'asc')->get();
    $clients = Client::all();
    $users = User::where('type', 'client')->orderBy('name', 'asc')->get();

        return view('closer-section.recall', [
            'update' => $update, 
            'clients' => $clients, 
            'closers' => $closers,
        'users' => $users
        ]);
    }

public function re_update(Request $request, $id)
{
    try {
        // 🔒 Authorization check
        if (!auth()->user()->can('closer re edit')) {
            // Handle AJAX requests
            if ($request->ajax()) {
                return response()->json([
                    'error' => 'You don\'t have permission to update records.',
                    'redirect' => route('dashboard')
                ], 403);
            }
            
            abort(403, 'You don\'t have permission to update records.');
        }

        // Find the existing ClosedCall record
        $closedCall = ClosedCall::findOrFail($id);

        // 🟩 Step 1: Validate the request data
        $validated = $request->validate([
            'customer_full_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'alternate_phone_number' => 'nullable|string|max:20',
            'cx_email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'city' => 'required|string|max:255',
            'state' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:10',
            'gender' => 'nullable|in:male,female,other',
            'martial_status' => 'nullable|in:single,married,divorced,widowed,separated',
            'age' => 'required|integer|min:1|max:120',
            'dob' => 'nullable|date|before:today',
            'palce_of_birth' => 'nullable|string|max:255',
            'height' => 'nullable|string|max:10',
            'weight' => 'nullable|integer|min:1|max:500',
            'closername' => 'required|string|max:255',
            'social_security' => 'nullable|string|max:20',
            'smoker' => 'nullable|in:yes,no',
            'health_condition' => 'nullable|string',
            'medication' => 'nullable|string',
            'hospital_name' => 'nullable|string|max:255',
            'hospital_address' => 'nullable|string|max:500',
            'physician_name' => 'nullable|string|max:255',
            'monthly_premium' => 'nullable|numeric|min:0',
            'carrier' => 'nullable|string|max:255',
            'coverage_plan' => 'nullable|string|max:255',
            'customer_eligibility' => 'nullable|in:level,Graded/Modified,Guaranteed Issue',
            'beneficiary' => 'nullable|string|max:255',
            'beneficiary_relation' => 'nullable|string|max:255',
            'beneficiary_phone' => 'nullable|string|max:20',
            'beneficiary_dob' => 'nullable|date|before:today',
            'payor' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'bank_address' => 'nullable|string|max:500',
            'routing_number' => 'nullable|string|max:50',
            'bank_account_number' => 'nullable|string|max:50',
            'debit_card_direct_express_no' => 'nullable|string|max:50',
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

        // 🟩 Step 2: Validate bank or card details requirement
        $hasBankDetails = $request->filled(['bank_name', 'routing_number', 'bank_account_number']);
        $hasDebitCardDetails = $request->filled(['debit_card_direct_express_no', 'debit_card_direct_express_expiration', 'debit_card_direct_express_cvv']);
        
        if (!$hasBankDetails && !$hasDebitCardDetails) {
            return back()->withErrors([
                'bank_or_card' => 'You must provide either bank details OR debit card details.',
            ])->withInput();
        }

        // 🟩 Step 3: Update only fields that are filled in payload
        $setIfFilled = function (string $field, ?string $column = null) use ($request, $validated, $closedCall) {
            if ($request->filled($field) && array_key_exists($field, $validated)) {
                $closedCall->{$column ?? $field} = $validated[$field];
            }
        };

        $setIfFilled('customer_full_name');
        $setIfFilled('phone_number');
        $setIfFilled('alternate_phone_number');
        $setIfFilled('cx_email');
        $setIfFilled('address');
        $setIfFilled('city');
        $setIfFilled('state');
        $setIfFilled('zip_code');
        $setIfFilled('gender');
        $setIfFilled('martial_status');
        $setIfFilled('age');

        if ($request->filled('dob') && !empty($validated['dob'])) {
            $closedCall->dob = date('Y-m-d', strtotime($validated['dob']));
        }

        $setIfFilled('palce_of_birth');
        $setIfFilled('height');
        $setIfFilled('weight');
        $setIfFilled('closername');
        $setIfFilled('social_security');
        $setIfFilled('smoker');
        $setIfFilled('health_condition');
        $setIfFilled('medication');
        $setIfFilled('hospital_name');
        $setIfFilled('hospital_address');
        $setIfFilled('physician_name');
        $setIfFilled('monthly_premium');
        $setIfFilled('carrier');
        $setIfFilled('coverage_plan');
        $setIfFilled('customer_eligibility');
        $setIfFilled('beneficiary');
        $setIfFilled('beneficiary_relation');
        $setIfFilled('beneficiary_phone');

        if ($request->filled('beneficiary_dob') && !empty($validated['beneficiary_dob'])) {
            $closedCall->beneficiary_dob = date('Y-m-d', strtotime($validated['beneficiary_dob']));
        }

        $setIfFilled('payor');
        $setIfFilled('bank_name');
        $setIfFilled('bank_address');
        $setIfFilled('routing_number');
        $setIfFilled('bank_account_number');
        $setIfFilled('debit_card_direct_express_no');

        if ($request->filled('debit_card_direct_express_expiration') && !empty($validated['debit_card_direct_express_expiration'])) {
            $closedCall->debit_card_direct_express_expiration = date('Y-m-d', strtotime($validated['debit_card_direct_express_expiration']));
        }

        $setIfFilled('debit_card_direct_express_cvv');
        $setIfFilled('account_type');

        if ($request->filled('initial_draft_date') && !empty($validated['initial_draft_date'])) {
            $closedCall->initial_draft_date = date('Y-m-d', strtotime($validated['initial_draft_date']));
        }

        if ($request->filled('future_draft_date') && !empty($validated['future_draft_date'])) {
            $closedCall->future_draft_date = date('Y-m-d', strtotime($validated['future_draft_date']));
        }

        $setIfFilled('underwriter_name');
        $setIfFilled('remarks');
        $closedCall->closer_id = auth()->user()->id;
        $setIfFilled('junior_closer_name');
        $setIfFilled('center_name');
        $setIfFilled('sale_made_by');
        $setIfFilled('agent_status');

        // Save the updated ClosedCall instance
        $closedCall->save();

        return redirect()->route('closer.closerview')->with('success', 'Policy updated successfully!');
        
    } catch (\Illuminate\Validation\ValidationException $e) {
        return redirect()->back()->withErrors($e->errors())->withInput();
    } catch (\Exception $e) {
        return redirect()->back()->withInput()->with('error', 'Error: ' . $e->getMessage());
    }
}

public function checkPhone(Request $request)
{
    $validated = $request->validate([
        'phone_number' => 'nullable|string|max:20',
        'alternate_phone_number' => 'nullable|string|max:20',
        'exclude_id' => 'nullable|integer',
    ]);

    $phoneNumber = $validated['phone_number'] ?? null;
    $altPhoneNumber = $validated['alternate_phone_number'] ?? null;

    if (empty($phoneNumber) && empty($altPhoneNumber)) {
        return response()->json(['exists' => false]);
    }

    $query = ClosedCall::query();

    if (!empty($validated['exclude_id'])) {
        $query->where('id', '!=', $validated['exclude_id']);
    }

    $existingRecord = $query->where(function ($q) use ($phoneNumber, $altPhoneNumber) {
        if (!empty($phoneNumber)) {
            $q->where('phone_number', $phoneNumber)
              ->orWhere('alternate_phone_number', $phoneNumber);
        }

        if (!empty($altPhoneNumber)) {
            $q->orWhere('phone_number', $altPhoneNumber)
              ->orWhere('alternate_phone_number', $altPhoneNumber);
        }
    })->first();

    if (!$existingRecord) {
        return response()->json(['exists' => false]);
    }

    return response()->json([
        'exists' => true,
        'record' => [
            'id' => $existingRecord->id,
            'customer_full_name' => $existingRecord->customer_full_name,
            'phone_number' => $existingRecord->phone_number,
            'alternate_phone_number' => $existingRecord->alternate_phone_number,
            'address' => $existingRecord->address,
            'city' => $existingRecord->city,
            'state' => $existingRecord->state,
            'carrier' => $existingRecord->carrier,
        ],
    ]);
}

public function searchExisting(Request $request)
{
    // 🔒 Authorization check - only allow users with proper permissions
    $user = auth()->user();
    
    if (!$user) {
        Log::warning('Unauthorized search attempt - no user', [
            'ip' => request()->ip()
        ]);
        abort(403, 'Unauthorized access.');
    }

    // Check if user has permission to search existing records
    $hasPermission = Gate::forUser($user)->allows('closed call records') || 
                    Gate::forUser($user)->allows('search existing records') ||
                    in_array($user->type, ['super admin', 'company', 'admin', 'Director', 'Project Manager', 'closer']);
    
    if (!$hasPermission) {
        Log::warning('Unauthorized search existing records attempt', [
            'user_id' => $user->id,
            'user_type' => $user->type,
            'ip' => request()->ip()
        ]);
        abort(403, 'You do not have permission to search existing records.');
    }

    $search = $request->input('search');
    $existingRecords = collect();
    $searched = false;
    $searchError = null;

    if ($search) {
        // 🔒 Security: Require minimum search length to prevent enumeration attacks
        $minSearchLength = 4; // Require at least 4 characters
        $search = trim($search);
        
        if (strlen($search) < $minSearchLength) {
            $searchError = "Search term must be at least {$minSearchLength} characters long.";
            Log::warning('Search term too short', [
                'user_id' => $user->id,
                'search_length' => strlen($search),
                'ip' => request()->ip()
            ]);
        } else {
            $searched = true;
            
            // Build query with user-based filtering
            $query = ClosedCall::query();
            
            // Apply user-based filtering based on user type
            if ($user->type === 'client') {
                // Client users can only search their own records
                $authUserEmail = $user->email;
                $client = Client::where('email', $authUserEmail)->first();
                
                if ($client) {
                    // PARENT CLIENT LOGIC
                    $clientId = $client->id;
                    $associatedUserIds = User::where('type', 'client')
                        ->where('client_id', $clientId)
                        ->pluck('id')
                        ->toArray();
                    
                    $associatedUserIds[] = $user->id;
                    $associatedUserIds = array_unique($associatedUserIds);
                    
                    if (!empty($associatedUserIds)) {
                        $query->whereIn('clients_id', $associatedUserIds);
                    } else {
                        $query->where('id', 0); // Return no results
                    }
                } else {
                    // CHILD CLIENT LOGIC
                    $query->where('clients_id', $user->id);
                }
            } elseif ($user->type === 'closer') {
                // Closers can only search their own records
                $query->where('closer_id', $user->id);
            }
            // For admin/super admin types, they can search all records (already checked permission above)
            
            // Search in both phone_number and alternate_phone_number fields
            $query->where(function($q) use ($search) {
                $q->where('phone_number', 'LIKE', "%{$search}%")
                  ->orWhere('alternate_phone_number', 'LIKE', "%{$search}%");
            });
            
            // 🔒 Security: Limit results to prevent data leakage
            $maxResults = 50; // Maximum number of results to return
            $existingRecords = $query->orderBy('created_at', 'desc')
                ->limit($maxResults)
                ->get();
            
            // Log the search for audit purposes
            Log::info('Existing records search executed', [
                'user_id' => $user->id,
                'user_type' => $user->type,
                'search_length' => strlen($search),
                'result_count' => $existingRecords->count(),
                'ip' => request()->ip()
            ]);
            
            // If results hit the limit, warn the user
            if ($existingRecords->count() >= $maxResults) {
                $searchError = "Search returned many results. Showing first {$maxResults} records. Please refine your search.";
            }
        }
    }

    return view('closer-section.search-existing', compact('existingRecords', 'search', 'searched', 'searchError'));
}

};
