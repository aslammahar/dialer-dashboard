<?php

namespace App\Http\Controllers;

use App\Models\ClosedCall;
use App\Models\User;
use App\Models\Client;
use App\Services\SalesService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;

class SalesReportController extends Controller
{
    // Define approved statuses as a constant
  private const APPROVED_STATUSES = [
     
        'Funded', 
        'charged_backed',
        'Approved',
        'Potential Lapsed',
        
    ];

     const PENDING_STATUSES = [
        'Pending',
        'Underwriting',
        'Need to Reach',
        'NSF',
    ];
    
    const REJECTED_STATUSES = [
        'Rejected',
        'DNC',
    ];
     const ALL_CARRIERS = [
        'Aetna', 'Aetna(CVS)', 'AFLAC', 'AIG', 'AmAm', 'Americo', 'Assurant', 
        'C5', 'CVS', 'Foresters', 'Globe Life', 'GW', 'GTL (Guarantee Trust Life)', 
        'Liberty Banker Life (LBL)', 'Lumico', 'Mutual of Omaha', 'Prosperity', 
        'RNA', 'Security National Life (SNL)', 'Sentinel Security Life (SSL)', 
        'Sons of Norway', 'Superior Choice (CICA)', 'TransAmerica','Securico Life'
    ];

    public function index(Request $request)
{
    
    $filter = $request->get('filter', 'monthly');
    $currentDate = Carbon::now('Asia/Karachi');

 if ($request->filled('month_year')) {
    [$year, $month] = explode('-', $request->month_year); // <-- swap order here

    $startDate = Carbon::createFromDate($year, $month, 1, 'Asia/Karachi')->startOfMonth();
    $endDate   = Carbon::createFromDate($year, $month, 1, 'Asia/Karachi')->endOfMonth();
    $previousStartDate = $startDate->copy()->subMonth()->startOfMonth();
    $previousEndDate   = $startDate->copy()->subMonth()->endOfMonth();

    $period = $startDate->format('F Y');
    $previousPeriod = $previousStartDate->format('F Y');
    $filter = 'custom';
} else {
        switch ($filter) {
            case 'daily':
                $adjustedDate = $currentDate->copy()->subHours(12);
                $startDate = $adjustedDate->copy()->startOfDay()->addHours(12);
                $endDate   = $adjustedDate->copy()->addDay()->startOfDay()->addHours(12)->subMinute();
                $previousAdjustedDate = $currentDate->copy()->subDay()->subHours(12);
                $previousStartDate = $previousAdjustedDate->copy()->startOfDay()->addHours(12);
                $previousEndDate   = $previousAdjustedDate->copy()->addDay()->startOfDay()->addHours(12)->subMinute();
                $period = 'Today';
                $previousPeriod = 'Yesterday';
                break;

            case 'weekly':
                $startDate = $currentDate->copy()->startOfWeek();
                $endDate   = $currentDate->copy()->endOfWeek();
                $previousStartDate = $currentDate->copy()->subWeek()->startOfWeek();
                $previousEndDate   = $currentDate->copy()->subWeek()->endOfWeek();
                $period = 'This Week';
                $previousPeriod = 'Last Week';
                break;

            default: // monthly
                $startDate = $currentDate->copy()->startOfMonth();
                $endDate   = $currentDate->copy()->endOfMonth();
                $previousStartDate = $currentDate->copy()->subMonth()->startOfMonth();
                $previousEndDate   = $currentDate->copy()->subMonth()->endOfMonth();
                $period = 'This Month';
                $previousPeriod = 'Last Month';
                break;
        }
    }

    // Convert to UTC for DB queries
    $utcStartDate = $startDate->copy()->utc();
    $utcEndDate = $endDate->copy()->utc();
    $utcPreviousStartDate = $previousStartDate->copy()->utc();
    $utcPreviousEndDate = $previousEndDate->copy()->utc();

    $currentData = $this->getCenterData($utcStartDate, $utcEndDate);
    $previousData = $this->getCenterData($utcPreviousStartDate, $utcPreviousEndDate);

    $jsonsGrowth = $this->calculateGrowth($previousData['jsons']['submissions'], $currentData['jsons']['submissions']);
    $sellerzGrowth = $this->calculateGrowth($previousData['sellerz']['submissions'], $currentData['sellerz']['submissions']);

    $chartData = $this->getChartData($filter, $currentDate);

    return view('sales.center-report', compact(
        'currentData',
        'previousData',
        'jsonsGrowth',
        'sellerzGrowth',
        'filter',
        'period',
        'previousPeriod',
        'chartData'
    ));
}

private function getCenterData($startDate, $endDate)
{
    // Get JSONS data
    $jsonsSubmissions = ClosedCall::where('center_name', 'jsons')
        ->whereBetween('created_at', [$startDate, $endDate])
        ->count();
        
    $jsonsApproved = ClosedCall::where('center_name', 'jsons')
        ->whereIn('status', self::APPROVED_STATUSES)
        ->whereBetween('created_at', [$startDate, $endDate])
        ->count();
        
    $jsonsAvgPremium = ClosedCall::where('center_name', 'jsons')
        ->whereBetween('created_at', [$startDate, $endDate])
        ->whereNotNull('monthly_premium')
        ->avg('monthly_premium');
    $jsonsAvgPremium = round($jsonsAvgPremium ?? 0, 2);
    
    $jsonsActiveClosers = ClosedCall::where('center_name', 'jsons')
        ->whereBetween('created_at', [$startDate, $endDate])
        ->whereNotNull('closername')
        ->distinct('closername')
        ->count('closername');
    $jsonsSubmissionsPerCloser = $jsonsActiveClosers > 0 ? round($jsonsSubmissions / $jsonsActiveClosers, 1) : 0;
    
    // Get SELLERZ data
    $sellerzSubmissions = ClosedCall::where('center_name', 'sellerz')
        ->whereBetween('created_at', [$startDate, $endDate])
        ->count();
        
    $sellerzApproved = ClosedCall::where('center_name', 'sellerz')
        ->whereIn('status', self::APPROVED_STATUSES)
        ->whereBetween('created_at', [$startDate, $endDate])
        ->count();
        
    $sellerzAvgPremium = ClosedCall::where('center_name', 'sellerz')
        ->whereBetween('created_at', [$startDate, $endDate])
        ->whereNotNull('monthly_premium')
        ->avg('monthly_premium');
    $sellerzAvgPremium = round($sellerzAvgPremium ?? 0, 2);
    
    $sellerzActiveClosers = ClosedCall::where('center_name', 'sellerz')
        ->whereBetween('created_at', [$startDate, $endDate])
        ->whereNotNull('closername')
        ->distinct('closername')
        ->count('closername');
    $sellerzSubmissionsPerCloser = $sellerzActiveClosers > 0 ? round($sellerzSubmissions / $sellerzActiveClosers, 1) : 0;
    
    // Calculate conversion rates
    $jsonsConversionRate = $jsonsSubmissions > 0 ? round(($jsonsApproved / $jsonsSubmissions) * 100, 2) : 0;
    $sellerzConversionRate = $sellerzSubmissions > 0 ? round(($sellerzApproved / $sellerzSubmissions) * 100, 2) : 0;
    
    // FIXED: Calculate customer eligibility percentages for JSONS APPROVED records
    $jsonsLevelCount = ClosedCall::where('center_name', 'jsons')
        ->whereBetween('created_at', [$startDate, $endDate])
        ->whereIn('status', self::APPROVED_STATUSES)
        ->whereIn('customer_eligibility', [
            'Level', 'Graded', 'Modified', 'Standard', 'Preferred', 
            'Senior choice immediate', 'Golden solution immediate', 
            'Senior choice graded', 'Golden solution graded', 
            'Senior choice rop', 'Golden solution rop', 
            'Express select', 'ROP'
        ])
        ->count();
        
    $jsonsGICount = ClosedCall::where('center_name', 'jsons')
        ->whereBetween('created_at', [$startDate, $endDate])
        ->whereIn('status', self::APPROVED_STATUSES)
        ->where(function($query) {
            $query->where('customer_eligibility', 'Guaranteed Issue')
                  ->orWhere('customer_eligibility', 'Graded GTL');
        })
        ->count();
    
    $jsonsLevelPercent = $jsonsApproved > 0 ? round(($jsonsLevelCount / $jsonsApproved) * 100) : 0;
    $jsonsGIPercent = $jsonsApproved > 0 ? round(($jsonsGICount / $jsonsApproved) * 100) : 0;
    
    // FIXED: Calculate customer eligibility percentages for SELLERZ APPROVED records
    $sellerzLevelCount = ClosedCall::where('center_name', 'sellerz')
        ->whereBetween('created_at', [$startDate, $endDate])
        ->whereIn('status', self::APPROVED_STATUSES)
        ->whereIn('customer_eligibility', [
            'Level', 'Graded', 'Modified', 'Standard', 'Preferred', 
            'Senior choice immediate', 'Golden solution immediate', 
            'Senior choice graded', 'Golden solution graded', 
            'Senior choice rop', 'Golden solution rop', 
            'Express select', 'ROP'
        ])
        ->count();
        
    $sellerzGICount = ClosedCall::where('center_name', 'sellerz')
        ->whereBetween('created_at', [$startDate, $endDate])
        ->whereIn('status', self::APPROVED_STATUSES)
        ->where(function($query) {
            $query->where('customer_eligibility', 'Guaranteed Issue')
                  ->orWhere('customer_eligibility', 'Graded GTL');
        })
        ->count();
    
    $sellerzLevelPercent = $sellerzApproved > 0 ? round(($sellerzLevelCount / $sellerzApproved) * 100) : 0;
    $sellerzGIPercent = $sellerzApproved > 0 ? round(($sellerzGICount / $sellerzApproved) * 100) : 0;
    
    // Ensure percentages don't exceed 100%
    $jsonsLevelPercent = min($jsonsLevelPercent, 100);
    $jsonsGIPercent = min($jsonsGIPercent, 100);
    $sellerzLevelPercent = min($sellerzLevelPercent, 100);
    $sellerzGIPercent = min($sellerzGIPercent, 100);
    
    // Handle edge case where percentages exceed 100% combined
    if ($jsonsLevelPercent + $jsonsGIPercent > 100) {
        $total = $jsonsLevelPercent + $jsonsGIPercent;
        $jsonsLevelPercent = round(($jsonsLevelPercent / $total) * 100);
        $jsonsGIPercent = round(($jsonsGIPercent / $total) * 100);
    }
    
    if ($sellerzLevelPercent + $sellerzGIPercent > 100) {
        $total = $sellerzLevelPercent + $sellerzGIPercent;
        $sellerzLevelPercent = round(($sellerzLevelPercent / $total) * 100);
        $sellerzGIPercent = round(($sellerzGIPercent / $total) * 100);
    }
    
    return [
        'jsons' => [
            'submissions' => $jsonsSubmissions,
            'approved' => $jsonsApproved,
            'conversion_rate' => $jsonsConversionRate,
            'pending' => $jsonsSubmissions - $jsonsApproved,
            'level_percent' => $jsonsLevelPercent,
            'gi_percent' => $jsonsGIPercent,
            'avg_premium' => $jsonsAvgPremium,
            'submissions_per_closer' => $jsonsSubmissionsPerCloser,
            'active_closers' => $jsonsActiveClosers
        ],
        'sellerz' => [
            'submissions' => $sellerzSubmissions,
            'approved' => $sellerzApproved,
            'conversion_rate' => $sellerzConversionRate,
            'pending' => $sellerzSubmissions - $sellerzApproved,
            'level_percent' => $sellerzLevelPercent,
            'gi_percent' => $sellerzGIPercent,
            'avg_premium' => $sellerzAvgPremium,
            'submissions_per_closer' => $sellerzSubmissionsPerCloser,
            'active_closers' => $sellerzActiveClosers
        ]
    ];
}

        
    private function calculateGrowth($previous, $current)
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }
        
        return round((($current - $previous) / $previous) * 100, 2);
    }
    
    private function getChartData($filter, $currentDate)
    {
        $chartData = [];
        
        switch ($filter) {
            case 'daily':
                // Last 7 days (adjusted for night shift) - convert to UTC for database queries
                for ($i = 6; $i >= 0; $i--) {
                    $date = $currentDate->copy()->subDays($i);
                    // Adjust for night shift - business day starts at 12:00 PM and ends at 11:59 AM next day
                    $adjustedDate = $date->copy()->subHours(12);
                    $startDate = $adjustedDate->copy()->startOfDay()->addHours(12)->utc(); // Convert to UTC
                    $endDate = $adjustedDate->copy()->addDay()->startOfDay()->addHours(12)->subMinute()->utc(); // Convert to UTC
                    
                    $jsonsCount = ClosedCall::where('center_name', 'jsons')
                        ->whereBetween('created_at', [$startDate, $endDate])
                        ->count();
                        
                    $sellerzCount = ClosedCall::where('center_name', 'sellerz')
                        ->whereBetween('created_at', [$startDate, $endDate])
                        ->count();
                    
                    $chartData[] = [
                        'date' => $date->format('M d'),
                        'jsons' => (int)$jsonsCount,
                        'sellerz' => (int)$sellerzCount
                    ];
                }
                break;
                
            case 'weekly':
                // Last 8 weeks - convert to UTC for database queries
                for ($i = 7; $i >= 0; $i--) {
                    $startDate = $currentDate->copy()->subWeeks($i)->startOfWeek()->utc();
                    $endDate = $currentDate->copy()->subWeeks($i)->endOfWeek()->utc();
                    
                    $jsonsCount = ClosedCall::where('center_name', 'jsons')
                        ->whereBetween('created_at', [$startDate, $endDate])
                        ->count();
                        
                    $sellerzCount = ClosedCall::where('center_name', 'sellerz')
                        ->whereBetween('created_at', [$startDate, $endDate])
                        ->count();
                    
                    $chartData[] = [
                        'date' => 'Week ' . $currentDate->copy()->subWeeks($i)->weekOfYear,
                        'jsons' => (int)$jsonsCount,
                        'sellerz' => (int)$sellerzCount
                    ];
                }
                break;
                
            default: // monthly
                // Last 6 months - convert to UTC for database queries
                for ($i = 5; $i >= 0; $i--) {
                    $startDate = $currentDate->copy()->subMonths($i)->startOfMonth()->utc();
                    $endDate = $currentDate->copy()->subMonths($i)->endOfMonth()->utc();
                    
                    $jsonsCount = ClosedCall::where('center_name', 'jsons')
                        ->whereBetween('created_at', [$startDate, $endDate])
                        ->count();
                        
                    $sellerzCount = ClosedCall::where('center_name', 'sellerz')
                        ->whereBetween('created_at', [$startDate, $endDate])
                        ->count();
                    
                    $chartData[] = [
                        'date' => $currentDate->copy()->subMonths($i)->format('M Y'),
                        'jsons' => (int)$jsonsCount,
                        'sellerz' => (int)$sellerzCount
                    ];
                }
                break;
        }
        
        return $chartData;
    }
    
    public function getTopPerformers(Request $request)
    {
        $filter = $request->get('filter', 'monthly');
        $currentDate = Carbon::now('Asia/Karachi'); // Pakistan timezone
        
        switch ($filter) {
            case 'daily':
                // Adjust for night shift - business day starts at 12:00 PM and ends at 11:59 AM next day
                $adjustedDate = $currentDate->copy()->subHours(12);
                $startDate = $adjustedDate->copy()->startOfDay()->addHours(12)->utc(); // Convert to UTC
                $endDate = $adjustedDate->copy()->addDay()->startOfDay()->addHours(12)->subMinute()->utc(); // Convert to UTC
                break;
            case 'weekly':
                $startDate = $currentDate->copy()->startOfWeek()->utc();
                $endDate = $currentDate->copy()->endOfWeek()->utc();
                break;
            default:
                $startDate = $currentDate->copy()->startOfMonth()->utc();
                $endDate = $currentDate->copy()->endOfMonth()->utc();
                break;
        }
        
        // Build approved count SQL with new status logic
        $approvedCountSql = 'SUM(CASE WHEN status IN ("' . implode('","', self::APPROVED_STATUSES) . '") THEN 1 ELSE 0 END)';
        $conversionRateSql = 'ROUND((' . $approvedCountSql . ' / COUNT(*)) * 100, 2)';
        
        // Get top performers by closername for JSONS center (group by closername only)
        $jsonsPerformers = ClosedCall::select('closername',
                DB::raw('MAX(closername) as closername'), // Pick one closername
                DB::raw('COUNT(*) as total_submissions'),
                DB::raw($approvedCountSql . ' as approved_count'),
                DB::raw($conversionRateSql . ' as conversion_rate')
            )
            ->where('center_name', 'jsons')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('closername')
            ->groupBy('closername') // Group by closername only
            ->orderByRaw('approved_count DESC, total_submissions DESC')
            ->limit(10)
            ->get();

        // Load user relationships for JSONS
        $jsonsPerformers->load('closer:id,name,email');
            
        // Get top performers by closername for SELLERZ center (group by closername only)
        $sellerzPerformers = ClosedCall::select('closername',
                DB::raw('MAX(closername) as closername'), // Pick one closername
                DB::raw('COUNT(*) as total_submissions'),
                DB::raw($approvedCountSql . ' as approved_count'),
                DB::raw($conversionRateSql . ' as conversion_rate')
            )
            ->where('center_name', 'sellerz')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('closername')
            ->groupBy('closername') // Group by closername only
            ->orderByRaw('approved_count DESC, total_submissions DESC')
            ->limit(10)
            ->get();

        // Load user relationships for SELLERZ
        $sellerzPerformers->load('closer:id,name,email');
        
        return response()->json([
            'jsons' => $jsonsPerformers,
            'sellerz' => $sellerzPerformers
        ]);
    }

    public function getTopClosersBySubmissions(Request $request)
    {
        $filter = $request->get('filter', 'monthly');
        $currentDate = Carbon::now('Asia/Karachi'); // Pakistan timezone
        
        switch ($filter) {
            case 'daily':
                $adjustedDate = $currentDate->copy()->subHours(12);
                $startDate = $adjustedDate->copy()->startOfDay()->addHours(12)->utc();
                $endDate = $adjustedDate->copy()->addDay()->startOfDay()->addHours(12)->subMinute()->utc();
                break;
            case 'weekly':
                $startDate = $currentDate->copy()->startOfWeek()->utc();
                $endDate = $currentDate->copy()->endOfWeek()->utc();
                break;
            default:
                $startDate = $currentDate->copy()->startOfMonth()->utc();
                $endDate = $currentDate->copy()->endOfMonth()->utc();
                break;
        }
        
        // Build approved count SQL with new status logic
        $approvedCountSql = 'SUM(CASE WHEN status IN ("' . implode('","', self::APPROVED_STATUSES) . '") THEN 1 ELSE 0 END)';
        $conversionRateSql = 'ROUND((' . $approvedCountSql . ' / COUNT(*)) * 100, 2)';
        
        // Get overall top closers by submissions (combine all centers per closer)
        $topBySubmissions = ClosedCall::select('closername',
                DB::raw('MAX(closername) as closername'), // Pick one closername
                DB::raw('GROUP_CONCAT(DISTINCT center_name) as center_name'), // Show all centers
                DB::raw('COUNT(*) as total_submissions'),
                DB::raw($approvedCountSql . ' as approved_count'),
                DB::raw($conversionRateSql . ' as conversion_rate')
            )
            ->with(['closer:id,name,email'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('closername')
            ->groupBy('closername') // Group by closername only (combine all centers)
            ->orderBy('total_submissions', 'desc')
            ->limit(10)
            ->get();
            
        // Get overall top closers by approved count (combine all centers per closer)
        $topByApproved = ClosedCall::select('closername',
                DB::raw('MAX(closername) as closername'), // Pick one closername
                DB::raw('GROUP_CONCAT(DISTINCT center_name) as center_name'), // Show all centers
                DB::raw('COUNT(*) as total_submissions'),
                DB::raw($approvedCountSql . ' as approved_count'),
                DB::raw($conversionRateSql . ' as conversion_rate')
            )
            ->with(['closer:id,name,email'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('closername')
            ->groupBy('closername') // Group by closername only (combine all centers)
            ->orderBy('approved_count', 'desc')
            ->limit(10)
            ->get();
        
        return response()->json([
            'top_by_submissions' => $topBySubmissions,
            'top_by_approved' => $topByApproved
        ]);
    }

public function client(Request $request)
{
    $filter = $request->get('filter', 'monthly');
    $currentDate = Carbon::now('Asia/Karachi');

    // ✅ PRIORITY 1: Handle specific date picker
    if ($request->filled('specific_date')) {
        $specificDate = Carbon::parse($request->specific_date, 'Asia/Karachi');
        
        // Adjust for night shift - business day starts at 12:00 PM and ends at 11:59 AM next day
        $adjustedDate = $specificDate->copy()->subHours(12);
        $startDate = $adjustedDate->copy()->startOfDay()->addHours(12)->utc();
        $endDate = $adjustedDate->copy()->addDay()->startOfDay()->addHours(12)->subMinute()->utc();
        
        $period = $specificDate->format('F d, Y');
        $filter = 'specific_date';
    }
    // ✅ PRIORITY 2: Handle month picker
    elseif ($request->filled('month_year')) {
        [$year, $month] = explode('-', $request->month_year);

        $startDate = Carbon::createFromDate($year, $month, 1, 'Asia/Karachi')->startOfMonth()->utc();
        $endDate   = Carbon::createFromDate($year, $month, 1, 'Asia/Karachi')->endOfMonth()->utc();
        $period = Carbon::createFromDate($year, $month, 1, 'Asia/Karachi')->format('F Y');
        $filter = 'custom';
    } 
    // ✅ PRIORITY 3: Regular filter buttons
    else {
        switch ($filter) {
            case 'daily':
                $adjustedDate = $currentDate->copy()->subHours(12);
                $startDate = $adjustedDate->copy()->startOfDay()->addHours(12)->utc();
                $endDate = $adjustedDate->copy()->addDay()->startOfDay()->addHours(12)->subMinute()->utc();
                $period = 'Today';
                break;

            case 'weekly':
                $startDate = $currentDate->copy()->startOfWeek()->utc();
                $endDate = $currentDate->copy()->endOfWeek()->utc();
                $period = 'This Week';
                break;

            default:
                $startDate = $currentDate->copy()->startOfMonth()->utc();
                $endDate = $currentDate->copy()->endOfMonth()->utc();
                $period = 'This Month';
                break;
        }
    }

    $clientReports = $this->getClientReports($startDate, $endDate);
    $carrierCounts = $this->getClientCarrierCounts($startDate, $endDate);
    $summary = $this->getSummaryStats($startDate, $endDate);

    return view('sales.client-report', compact(
        'clientReports',
        'carrierCounts',
        'summary',
        'filter',
        'period',
        'startDate',
        'endDate'
    ));
}

public function getClientDetails(Request $request)
{
    try {
        $clientId = $request->get('client_id');
        $filter = $request->get('filter', 'monthly');
        $currentDate = Carbon::now('Asia/Karachi');
        
        if (!$clientId) {
            return response()->json(['error' => 'Client ID required'], 400);
        }
        
        // ✅ PRIORITY 1: Handle specific date
        if ($request->filled('specific_date')) {
            $specificDate = Carbon::parse($request->specific_date, 'Asia/Karachi');
            $adjustedDate = $specificDate->copy()->subHours(12);
            $startDate = $adjustedDate->copy()->startOfDay()->addHours(12)->utc();
            $endDate = $adjustedDate->copy()->addDay()->startOfDay()->addHours(12)->subMinute()->utc();
        }
        // ✅ PRIORITY 2: Handle month picker
        elseif ($request->filled('month_year')) {
            [$year, $month] = explode('-', $request->month_year);
            $startDate = Carbon::createFromDate($year, $month, 1, 'Asia/Karachi')->startOfMonth()->utc();
            $endDate = Carbon::createFromDate($year, $month, 1, 'Asia/Karachi')->endOfMonth()->utc();
        }
        // ✅ PRIORITY 3: Regular filters
        else {
            switch ($filter) {
                case 'daily':
                    $adjustedDate = $currentDate->copy()->subHours(12);
                    $startDate = $adjustedDate->copy()->startOfDay()->addHours(12)->utc();
                    $endDate = $adjustedDate->copy()->addDay()->startOfDay()->addHours(12)->subMinute()->utc();
                    break;
                case 'weekly':
                    $startDate = $currentDate->copy()->startOfWeek()->utc();
                    $endDate = $currentDate->copy()->endOfWeek()->utc();
                    break;
                default:
                    $startDate = $currentDate->copy()->startOfMonth()->utc();
                    $endDate = $currentDate->copy()->endOfMonth()->utc();
                    break;
            }
        }
        
        // Get client basic info
        $client = User::find($clientId);
        if (!$client) {
            return response()->json(['error' => 'Client not found'], 404);
        }
        
        // Get detailed submissions for this client
        $submissions = ClosedCall::where('clients_id', $clientId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get(['id', 'customer_full_name', 'status', 'created_at', 'closername', 'monthly_premium','clients_comment']);
        
        // Calculate premium statistics ONLY for approved records
        $premiumStats = ClosedCall::where('clients_id', $clientId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('status', self::APPROVED_STATUSES)
            ->whereNotNull('monthly_premium')
            ->where('monthly_premium', '>', 0)
            ->selectRaw('
                AVG(monthly_premium) as avg_premium,
                SUM(monthly_premium) as total_premium,
                MIN(monthly_premium) as min_premium,
                MAX(monthly_premium) as max_premium,
                COUNT(*) as premium_count
            ')
            ->first();
        
        // Calculate customer eligibility percentages ONLY for APPROVED records
        $totalApproved = $submissions->whereIn('status', self::APPROVED_STATUSES)->count();
        $levelCount = ClosedCall::where('clients_id', $clientId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('status', self::APPROVED_STATUSES)
            ->where(function($query) {
                $query->whereIn('customer_eligibility', ['Level', 'Graded', 'Modified', 'Standard', 'Preferred', 'Senior choice immediate', 'Golden solution immediate', 'Senior choice graded', 'Golden solution graded', 'Senior choice rop', 'Golden solution rop', 'Express select', 'ROP']);
            })
            ->count();
            
        $giCount = ClosedCall::where('clients_id', $clientId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('status', self::APPROVED_STATUSES)
            ->where(function($query) {
                $query->where('customer_eligibility', 'Guaranteed Issue')
                      ->orWhere('customer_eligibility', 'Graded GTL');
            })
            ->count();
        
        // Calculate percentages based on APPROVED records only
        $levelPercent = $totalApproved > 0 ? round(($levelCount / $totalApproved) * 100) : 0;
        $giPercent = $totalApproved > 0 ? round(($giCount / $totalApproved) * 100) : 0;
        
        // Handle edge case where percentages don't add up to 100% due to rounding
        if ($levelPercent + $giPercent > 100) {
            $levelPercent = 100 - $giPercent;
        } else if ($levelPercent + $giPercent < 100 && $totalApproved > 0) {
            $levelPercent += 100 - ($levelPercent + $giPercent);
        }
        
        // Get stats
        $stats = [
            'total_submissions' => $submissions->count(),
            'approved_count' => $totalApproved,
            'pending_count' => $submissions->whereNotIn('status', self::APPROVED_STATUSES)->count(),
            'conversion_rate' => $submissions->count() > 0 
                ? round(($totalApproved / $submissions->count()) * 100, 2) 
                : 0,
            'level_percent' => $levelPercent,
            'gi_percent' => $giPercent,
            'avg_premium' => round($premiumStats->avg_premium ?? 0, 2),
            'total_premium' => round($premiumStats->total_premium ?? 0, 2),
            'min_premium' => round($premiumStats->min_premium ?? 0, 2),
            'max_premium' => round($premiumStats->max_premium ?? 0, 2),
            'yearly_estimate' => round(($premiumStats->total_premium ?? 0) * 12, 2),
            'premium_submissions' => $premiumStats->premium_count ?? 0
        ];
        
        return response()->json([
            'client' => [
                'id' => $client->id,
                'name' => $client->name,
                'email' => $client->email
            ],
            'stats' => $stats,
            'submissions' => $submissions,
            'period' => [
                'filter' => $filter,
                'start_date' => $startDate->format('Y-m-d H:i:s'),
                'end_date' => $endDate->format('Y-m-d H:i:s')
            ]
        ]);
        
    } catch (\Exception $e) {
        Log::error('Client details error: ' . $e->getMessage());
        return response()->json(['error' => $e->getMessage()], 500);
    }
}

public function exportClientReport(Request $request)
{
    try {
        $user = auth()->user();
        
        if (!$user) {
            Log::warning('Unauthorized export attempt - no user', [
                'ip' => request()->ip()
            ]);
            abort(403, 'Unauthorized access.');
        }

        $hasPermission = Gate::forUser($user)->allows('view client reports') || 
                        Gate::forUser($user)->allows('export client reports') ||
                        in_array($user->type, ['super admin', 'admin', 'Director', 'Project Manager']);
        
        if (!$hasPermission) {
            Log::warning('Unauthorized client report export attempt', [
                'user_id' => $user->id,
                'user_type' => $user->type,
                'ip' => request()->ip()
            ]);
            abort(403, 'You do not have permission to export client reports.');
        }

        $filter = $request->get('filter', 'monthly');
        $currentDate = Carbon::now('Asia/Karachi');
        
        // ✅ PRIORITY 1: Handle specific date
        if ($request->filled('specific_date')) {
            $specificDate = Carbon::parse($request->specific_date, 'Asia/Karachi');
            $adjustedDate = $specificDate->copy()->subHours(12);
            $startDate = $adjustedDate->copy()->startOfDay()->addHours(12)->utc();
            $endDate = $adjustedDate->copy()->addDay()->startOfDay()->addHours(12)->subMinute()->utc();
            $period = $specificDate->format('F_d_Y');
        }
        // ✅ PRIORITY 2: Handle month picker
        elseif ($request->filled('month_year')) {
            [$year, $month] = explode('-', $request->month_year);
            $startDate = Carbon::createFromDate($year, $month, 1, 'Asia/Karachi')->startOfMonth()->utc();
            $endDate = Carbon::createFromDate($year, $month, 1, 'Asia/Karachi')->endOfMonth()->utc();
            $period = Carbon::createFromDate($year, $month, 1)->format('F_Y');
        }
        // ✅ PRIORITY 3: Regular filters
        else {
            switch ($filter) {
                case 'daily':
                    $adjustedDate = $currentDate->copy()->subHours(12);
                    $startDate = $adjustedDate->copy()->startOfDay()->addHours(12)->utc();
                    $endDate = $adjustedDate->copy()->addDay()->startOfDay()->addHours(12)->subMinute()->utc();
                    $period = 'Daily';
                    break;
                case 'weekly':
                    $startDate = $currentDate->copy()->startOfWeek()->utc();
                    $endDate = $currentDate->copy()->endOfWeek()->utc();
                    $period = 'Weekly';
                    break;
                default:
                    $startDate = $currentDate->copy()->startOfMonth()->utc();
                    $endDate = $currentDate->copy()->endOfMonth()->utc();
                    $period = 'Monthly';
                    break;
            }
        }
        
        $clientReports = $this->getClientReports($startDate, $endDate, $user);
        
        $filename = "client_report_{$period}_" . $currentDate->format('Y_m_d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        
        $callback = function() use ($clientReports, $period, $startDate, $endDate, $user) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, ["Client Report - $period"]);
            fputcsv($file, ["Period: " . $startDate->format('Y-m-d') . " to " . $endDate->format('Y-m-d')]);
            fputcsv($file, ["Generated: " . now()->format('Y-m-d H:i:s')]);
            fputcsv($file, ["Note: Premium and eligibility data based on APPROVED records only"]);
            fputcsv($file, []);
            
            fputcsv($file, [
                'Client ID',
                'Client Name', 
                'Email',
                'Total Submissions',
                'Approved',
                'Pending',
                'Conversion Rate (%)',
                'Average Premium ($) - Approved Only',
                'Yearly Premium Estimate ($) - Approved Only',
                'Level (%) - Approved Only',
                'GI (%) - Approved Only'
            ]);
            
            $canViewSensitiveData = Gate::forUser($user)->allows('view sensitive data') || 
                                   Gate::forUser($user)->allows('export all data') ||
                                   in_array($user->type ?? '', ['super admin', 'admin', 'Director']);
            
            foreach ($clientReports as $report) {
                $clientEmail = $canViewSensitiveData ? ($report->client_email ?? '') : '***REDACTED***';
                
                fputcsv($file, [
                    $report->clients_id,
                    $report->client_name ?? 'Unknown',
                    $clientEmail,
                    $report->total_submissions ?? 0,
                    $report->approved_count ?? 0,
                    $report->pending_count ?? 0,
                    $report->conversion_rate ?? 0,
                    $report->avg_premium ?? 0,
                    $report->yearly_premium_estimate ?? 0,
                    $report->level_percent ?? 0,
                    $report->gi_percent ?? 0
                ]);
            }
            
            fclose($file);
        };
        
        Log::info('Client report export executed', [
            'user_id' => $user->id,
            'user_type' => $user->type,
            'filter' => $filter,
            'period' => $period,
            'record_count' => $clientReports->count(),
            'ip' => request()->ip()
        ]);
        
        return response()->stream($callback, 200, $headers);
        
    } catch (\Illuminate\Auth\AuthenticationException $e) {
        Log::warning('Unauthenticated export attempt', [
            'ip' => request()->ip()
        ]);
        abort(403, 'Unauthorized access.');
    } catch (\Exception $e) {
        Log::error('Export error: ' . $e->getMessage(), [
            'user_id' => auth()->id(),
            'trace' => $e->getTraceAsString()
        ]);
        return response()->json(['error' => 'Export failed: ' . $e->getMessage()], 500);
    }
}



private function getClientReports($startDate, $endDate, $user = null)
{
    try {
        // Build approved count SQL with new status logic
        $approvedCountSql = 'SUM(CASE WHEN status IN ("' . implode('","', self::APPROVED_STATUSES) . '") THEN 1 ELSE 0 END)';
        $conversionRateSql = 'ROUND((' . $approvedCountSql . ' / COUNT(*)) * 100, 2)';
        
        // UPDATED: Premium calculations ONLY for approved records
        $avgPremiumSql = 'ROUND(AVG(CASE WHEN status IN ("' . implode('","', self::APPROVED_STATUSES) . '") AND monthly_premium IS NOT NULL AND monthly_premium > 0 THEN monthly_premium END), 2)';
        $totalPremiumSql = 'SUM(CASE WHEN status IN ("' . implode('","', self::APPROVED_STATUSES) . '") AND monthly_premium IS NOT NULL AND monthly_premium > 0 THEN monthly_premium ELSE 0 END)';
        
        // Build query with user-based filtering
        $query = ClosedCall::select(
                'clients_id',
                DB::raw('COUNT(*) as total_submissions'),
                DB::raw($approvedCountSql . ' as approved_count'),
                DB::raw($conversionRateSql . ' as conversion_rate'),
                DB::raw($avgPremiumSql . ' as avg_premium'),
                DB::raw($totalPremiumSql . ' as total_premium_volume')
            )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('clients_id');
        
        // Apply user-based filtering if user is provided
        if ($user) {
            if ($user->type === 'client') {
                $authUserEmail = $user->email;
                $client = Client::where('email', $authUserEmail)->first();
                
                if ($client) {
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
                        $query->where('id', 0);
                    }
                } else {
                    $query->where('clients_id', $user->id);
                }
            }
        }
        
        $clientReports = $query->groupBy('clients_id')
            ->orderBy('total_submissions', 'desc')
            ->get();

        // Load client relationships and add client names plus customer eligibility calculations
        $clientReports->each(function($report) use ($startDate, $endDate) {
            $client = User::find($report->clients_id);
            $report->client_name = $client ? $client->name : 'Unknown Client';
            $report->client_email = $client ? $client->email : '';
            $report->pending_count = $report->total_submissions - $report->approved_count;
            
            $report->yearly_premium_estimate = round(($report->total_premium_volume ?? 0) * 12, 2);
            $report->avg_premium = $report->avg_premium ?? 0;
            
            // FIXED: Calculate customer eligibility percentages ONLY for APPROVED records
            $levelCount = ClosedCall::where('clients_id', $report->clients_id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->whereIn('status', self::APPROVED_STATUSES)
                ->whereIn('customer_eligibility', [
                    'Level', 'Graded', 'Modified', 'Standard', 'Preferred', 
                    'Senior choice immediate', 'Golden solution immediate', 
                    'Senior choice graded', 'Golden solution graded', 
                    'Senior choice rop', 'Golden solution rop', 
                    'Express select', 'ROP'
                ])
                ->count();
                
            $giCount = ClosedCall::where('clients_id', $report->clients_id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->whereIn('status', self::APPROVED_STATUSES)
                ->where(function($query) {
                    $query->where('customer_eligibility', 'Guaranteed Issue')
                          ->orWhere('customer_eligibility', 'Graded GTL');
                })
                ->count();
            
            // Calculate percentages based on APPROVED records only
            $report->level_percent = $report->approved_count > 0 ? round(($levelCount / $report->approved_count) * 100) : 0;
            $report->gi_percent = $report->approved_count > 0 ? round(($giCount / $report->approved_count) * 100) : 0;
            
            // Ensure percentages don't exceed 100%
            $report->level_percent = min($report->level_percent, 100);
            $report->gi_percent = min($report->gi_percent, 100);
            
            // Handle edge case where percentages exceed 100% combined
            if ($report->level_percent + $report->gi_percent > 100) {
                $total = $report->level_percent + $report->gi_percent;
                $report->level_percent = round(($report->level_percent / $total) * 100);
                $report->gi_percent = round(($report->gi_percent / $total) * 100);
            }
        });

        return $clientReports;
        
    } catch (\Exception $e) {
        Log::error('Client reports error: ' . $e->getMessage());
        return collect();
    }
}

private function getClientCarrierCounts($startDate, $endDate)
{
    try {
        // Get all clients who had submissions in this period
        $clientIds = ClosedCall::whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('clients_id')
            ->distinct('clients_id')
            ->pluck('clients_id');
        
        $carrierCounts = [];
        
        foreach ($clientIds as $clientId) {
            $client = User::find($clientId);
            if (!$client) continue;
            
            $clientCarriers = [];
            
            // Count each carrier for this client
            foreach (self::ALL_CARRIERS as $carrier) {
                $count = ClosedCall::where('clients_id', $clientId)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->whereIn('status', self::APPROVED_STATUSES) // ONLY APPROVED RECORDS
                    ->where('carrier', $carrier)
                    ->count();
                
                $clientCarriers[$carrier] = $count > 0 ? $count : '-';
            }
            
            $carrierCounts[] = [
                'client_id' => $clientId,
                'client_name' => $client->name,
                'client_email' => $client->email,
                'carriers' => $clientCarriers,
                'total_approved' => ClosedCall::where('clients_id', $clientId)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->whereIn('status', self::APPROVED_STATUSES)
                    ->count()
            ];
        }
        
        // Sort by client name
        usort($carrierCounts, function($a, $b) {
            return strcmp($a['client_name'], $b['client_name']);
        });
        
        return $carrierCounts;
        
    } catch (\Exception $e) {
        Log::error('Client carrier counts error: ' . $e->getMessage());
        return [];
    }
}

private function getSummaryStats($startDate, $endDate)
{
    try {
        $totalSubmissions = ClosedCall::whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('clients_id')
            ->count();
            
        $totalApproved = ClosedCall::whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('clients_id')
            ->whereIn('status', self::APPROVED_STATUSES)
            ->count();
            
        $totalClients = ClosedCall::whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('clients_id')
            ->distinct('clients_id')
            ->count();
            
        $averageConversion = $totalSubmissions > 0 
            ? round(($totalApproved / $totalSubmissions) * 100, 2) 
            : 0;
            
        // UPDATED: Calculate overall average premium ONLY from approved records
        $overallAvgPremium = ClosedCall::whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('clients_id')
            ->whereIn('status', self::APPROVED_STATUSES) // ONLY APPROVED RECORDS
            ->whereNotNull('monthly_premium')
            ->where('monthly_premium', '>', 0)
            ->avg('monthly_premium');
        $overallAvgPremium = round($overallAvgPremium ?? 0, 2);
        
        // UPDATED: Calculate overall yearly premium estimate from approved records
        $overallYearlyEstimate = $overallAvgPremium * 12;
            
        $topClient = ClosedCall::select(
                'clients_id',
                DB::raw('COUNT(*) as total_submissions')
            )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('clients_id')
            ->groupBy('clients_id')
            ->orderBy('total_submissions', 'desc')
            ->first();
            
        $topClientName = 'N/A';
        if ($topClient) {
            $client = User::find($topClient->clients_id);
            $topClientName = $client ? $client->name : 'Unknown';
        }

        return [
            'total_submissions' => $totalSubmissions,
            'total_approved' => $totalApproved,
            'total_pending' => $totalSubmissions - $totalApproved,
            'total_clients' => $totalClients,
            'average_conversion' => $averageConversion,
            'average_premium' => $overallAvgPremium,
            'yearly_premium_estimate' => round($overallYearlyEstimate, 2), // NEW
            'top_client_name' => $topClientName,
            'top_client_submissions' => $topClient ? $topClient->total_submissions : 0
        ];
        
    } catch (\Exception $e) {
        Log::error('Summary stats error: ' . $e->getMessage());
        return [
            'total_submissions' => 0,
            'total_approved' => 0,
            'total_pending' => 0,
            'total_clients' => 0,
            'average_conversion' => 0,
            'average_premium' => 0,
            'yearly_premium_estimate' => 0,
            'top_client_name' => 'N/A',
            'top_client_submissions' => 0
        ];
    }
}





public function CloserReport(Request $request)
{
    $currentDate = Carbon::now('America/New_York');

    // ✅ PRIORITY 1: Handle specific date picker
    if ($request->filled('specific_date')) {
        $specificDate = Carbon::parse($request->specific_date, 'America/New_York');

        $startDate = $specificDate->copy()->startOfDay()->utc();
        $endDate   = $specificDate->copy()->endOfDay()->utc();

        $period = $specificDate->format('F d, Y');
        $filter = 'specific_date';
    }
    // ✅ PRIORITY 2: Handle month picker
    elseif ($request->filled('month_year')) {
        [$year, $month] = explode('-', $request->month_year);

        $startDate = Carbon::createFromDate($year, $month, 1, 'America/New_York')->startOfMonth()->utc();
        $endDate   = Carbon::createFromDate($year, $month, 1, 'America/New_York')->endOfMonth()->utc();
        $period    = Carbon::createFromDate($year, $month, 1, 'America/New_York')->format('F Y');
        $filter    = 'custom';
    }
    // ✅ PRIORITY 3: Regular filter buttons
    else {
        $filter = $request->get('filter', 'monthly');

        switch ($filter) {
            case 'daily':
                $startDate = $currentDate->copy()->startOfDay()->utc();
                $endDate   = $currentDate->copy()->endOfDay()->utc();
                $period    = 'Today';
                break;

            case 'weekly':
                $startDate = $currentDate->copy()->startOfWeek()->utc();
                $endDate   = $currentDate->copy()->endOfWeek()->utc();
                $period    = 'This Week';
                break;

            default: // monthly
                $startDate = $currentDate->copy()->startOfMonth()->utc();
                $endDate   = $currentDate->copy()->endOfMonth()->utc();
                $period    = 'This Month';
                break;
        }
    }

    $closerReports = $this->getCloserReports($startDate, $endDate);
    $competition   = $this->getCompetitionData($startDate, $endDate);
    $summary       = $this->getSummaryStatsCloser($startDate, $endDate);

    return view('sales.closer-report', compact(
        'closerReports',
        'competition',
        'summary',
        'filter',
        'period',
        'startDate',
        'endDate'
    ));
}


   
    private function getCloserReports($startDate, $endDate)
{
    try {
        // Build approved count SQL with new status logic
        $approvedCountSql = 'SUM(CASE WHEN status IN ("' . implode('","', self::APPROVED_STATUSES) . '") THEN 1 ELSE 0 END)';
        $conversionRateSql = 'ROUND((' . $approvedCountSql . ' / COUNT(*)) * 100, 2)';
        
        // First, get closers WITH closername (group by closername and center_name only) - WITH PREMIUM
        $closersWithId = ClosedCall::select(
                'closername',
                'center_name',
                DB::raw('MAX(closername) as closername'), // Take any non-null closername
                DB::raw('COUNT(*) as total_submissions'),
                DB::raw($approvedCountSql . ' as approved_count'),
                DB::raw($conversionRateSql . ' as conversion_rate'),
                // Add average premium calculation
                DB::raw('ROUND(AVG(CASE WHEN monthly_premium IS NOT NULL AND monthly_premium > 0 THEN monthly_premium END), 2) as avg_premium'),
                DB::raw('SUM(CASE WHEN monthly_premium IS NOT NULL AND monthly_premium > 0 THEN monthly_premium ELSE 0 END) as total_premium_volume')
            )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('closername')
            ->where('agent_status', 'Sale made') // Add this filter
            ->groupBy('closername', 'center_name')
            ->get();

        // Second, get closers WITHOUT closername (external closers) - WITH PREMIUM
        $closersWithoutId = ClosedCall::select(
                DB::raw('NULL as closername'),
                'closername',
                'center_name',
                DB::raw('COUNT(*) as total_submissions'),
                DB::raw($approvedCountSql . ' as approved_count'),
                DB::raw($conversionRateSql . ' as conversion_rate'),
                // Add average premium calculation
                DB::raw('ROUND(AVG(CASE WHEN monthly_premium IS NOT NULL AND monthly_premium > 0 THEN monthly_premium END), 2) as avg_premium'),
                DB::raw('SUM(CASE WHEN monthly_premium IS NOT NULL AND monthly_premium > 0 THEN monthly_premium ELSE 0 END) as total_premium_volume')
            )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNull('closername')
            ->whereNotNull('closername')
            ->where('agent_status', 'Sale made') // Add this filter
            ->groupBy('closername', 'center_name')
            ->get();

        // Combine both collections
        $closerReports = $closersWithId->concat($closersWithoutId);

        // Sort by approved count and submissions
        $closerReports = $closerReports->sortByDesc(function($report) {
            return [$report->approved_count, $report->total_submissions];
        });

        // Enhance data with user information and calculations
        $closerReports->each(function($report) {
            // Get closer user information
            if ($report->closername) {
                $closer = User::find($report->closername);
                $report->closer_name = $closer ? $closer->name : ($report->closername ?: 'Unknown');
                $report->closer_email = $closer ? $closer->email : '';
                $report->designation = $this->getDesignation($closer);
            } else {
                $report->closer_name = $report->closername ?: 'Unknown';
                $report->closer_email = '';
                $report->designation = 'External Closer';
            }
            
            // Calculate additional metrics
            $report->pending_count = $report->total_submissions - $report->approved_count;
            $report->sales_count = $report->approved_count; // Sales = Approved for this context
            $report->company = $report->center_name ? strtoupper($report->center_name) : 'Unknown';
            
            // Calculate estimated yearly premium volume
            $report->yearly_premium_estimate = round(($report->total_premium_volume ?? 0) * 12, 2);
            
            // Handle null average premium
            $report->avg_premium = $report->avg_premium ?? 0;
            
            // Performance rating
            $report->performance_rating = $this->calculatePerformanceRating(
                $report->conversion_rate, 
                $report->total_submissions
            );
        });

        return $closerReports->values(); // Reset array keys
        
    } catch (\Exception $e) {
        Log::error('Closer reports error: ' . $e->getMessage());
        return collect();
    }
}
    
    private function getCompetitionData($startDate, $endDate)
    {
        try {
            // Build approved count SQL with new status logic
            $approvedCountSql = 'SUM(CASE WHEN status IN ("' . implode('","', self::APPROVED_STATUSES) . '") THEN 1 ELSE 0 END)';
            $conversionRateSql = 'ROUND((' . $approvedCountSql . ' / COUNT(*)) * 100, 2)';
            
            // Get top closers by total performance across all centers
            $topClosers = ClosedCall::select(
                    'closername',
                    'closername',
                    DB::raw('GROUP_CONCAT(DISTINCT center_name) as centers'),
                    DB::raw('COUNT(*) as total_submissions'),
                    DB::raw($approvedCountSql . ' as approved_count'),
                    DB::raw($conversionRateSql . ' as conversion_rate')
                )
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where(function($query) {
                    $query->whereNotNull('closername')
                          ->orWhereNotNull('closername');
                })
                // Group by closer only (combine all centers for competition)
                ->groupBy('closername', 'closername')
                ->orderBy('approved_count', 'desc')
                ->orderBy('total_submissions', 'desc')
                ->limit(2)
                ->get();

            // Enhance competition data
            $competition = [];
            foreach ($topClosers as $index => $closer) {
                $user = null;
                if ($closer->closername) {
                    $user = User::find($closer->closername);
                }
                
                $competition[] = [
                    'rank' => $index + 1,
                    'closername' => $closer->closername,
                    'name' => $user ? $user->name : ($closer->closername ?: 'Unknown'),
                    'email' => $user ? $user->email : '',
                    'center' => $closer->centers ?: 'Unknown',
                    'submissions' => $closer->total_submissions,
                    'approved' => $closer->approved_count,
                    'conversion_rate' => $closer->conversion_rate,
                    'pending' => $closer->total_submissions - $closer->approved_count
                ];
            }
            
            // Fill empty slots if less than 2 closers
            while (count($competition) < 2) {
                $competition[] = [
                    'rank' => count($competition) + 1,
                    'closername' => null,
                    'name' => 'No Data',
                    'email' => '',
                    'center' => 'N/A',
                    'submissions' => 0,
                    'approved' => 0,
                    'conversion_rate' => 0,
                    'pending' => 0
                ];
            }

            return $competition;
            
        } catch (\Exception $e) {
            Log::error('Competition data error: ' . $e->getMessage());
            return [];
        }
    }
    
    private function getSummaryStatsCloser($startDate, $endDate)
    {
        try {
            $totalSubmissions = ClosedCall::whereBetween('created_at', [$startDate, $endDate])
                ->where(function($query) {
                    $query->whereNotNull('closername')
                          ->orWhereNotNull('closername');
                })
                ->count();
                
            $totalApproved = ClosedCall::whereBetween('created_at', [$startDate, $endDate])
                ->where(function($query) {
                    $query->whereNotNull('closername')
                          ->orWhereNotNull('closername');
                })
                ->whereIn('status', self::APPROVED_STATUSES)
                ->count();
                
            // Fixed active closers calculation
            $activeClosersQuery = ClosedCall::whereBetween('created_at', [$startDate, $endDate])
                ->where(function($query) {
                    $query->whereNotNull('closername')
                          ->orWhereNotNull('closername');
                });
                
            // Count distinct closers (either by closername or closername)
            $activeClosersByIds = $activeClosersQuery->whereNotNull('closername')
                ->distinct('closername')
                ->count('closername');
                
            $activeClosersByNames = ClosedCall::whereBetween('created_at', [$startDate, $endDate])
                ->whereNull('closername')
                ->whereNotNull('closername')
                ->distinct('closername')
                ->count('closername');
                
            $activeClosers = $activeClosersByIds + $activeClosersByNames;
                
            $averageConversion = $totalSubmissions > 0 
                ? round(($totalApproved / $totalSubmissions) * 100, 2) 
                : 0;
            
            // Calculate overall average premium across all closers
            $overallAvgPremium = ClosedCall::whereBetween('created_at', [$startDate, $endDate])
                ->where(function($query) {
                    $query->whereNotNull('closername')
                          ->orWhereNotNull('closername');
                })
                ->whereNotNull('monthly_premium')
                ->where('monthly_premium', '>', 0)
                ->avg('monthly_premium');
            $overallAvgPremium = round($overallAvgPremium ?? 0, 2);
            
            // Build approved count SQL with new status logic
            $approvedCountSql = 'SUM(CASE WHEN status IN ("' . implode('","', self::APPROVED_STATUSES) . '") THEN 1 ELSE 0 END)';
            
            // Get center breakdown
            $centerStats = ClosedCall::select(
                    'center_name',
                    DB::raw('COUNT(*) as submissions'),
                    DB::raw($approvedCountSql . ' as approved')
                )
                ->whereBetween('created_at', [$startDate, $endDate])
                ->whereNotNull('center_name')
                ->groupBy('center_name')
                ->get();

            return [
                'total_submissions' => $totalSubmissions,
                'total_approved' => $totalApproved,
                'total_sales' => $totalApproved, // Sales = Approved
                'total_pending' => $totalSubmissions - $totalApproved,
                'active_closers' => $activeClosers,
                'average_conversion' => $averageConversion,
                'average_premium' => $overallAvgPremium,
                'center_stats' => $centerStats
            ];
            
        } catch (\Exception $e) {
            Log::error('Summary stats error: ' . $e->getMessage());
            return [
                'total_submissions' => 0,
                'total_approved' => 0,
                'total_sales' => 0,
                'total_pending' => 0,
                'active_closers' => 0,
                'average_conversion' => 0,
                'average_premium' => 0,
                'center_stats' => collect()
            ];
        }
    }
    
    private function getDesignation($user)
    {
        if (!$user) return 'External Closer';
        
        // You can customize this based on your user roles/permissions
        // For now, using a simple designation system
        if (isset($user->role)) {
            switch ($user->role) {
                case 'admin': return 'Senior Closer';
                case 'manager': return 'Lead Closer';
                case 'closer': return 'Sales Closer';
                default: return 'Sales Representative';
            }
        }
        
        return 'Sales Closer';
    }
    
    private function calculatePerformanceRating($conversionRate, $totalSubmissions)
    {
        // Performance rating based on conversion rate and volume
        if ($conversionRate >= 80 && $totalSubmissions >= 10) return 'Excellent';
        if ($conversionRate >= 60 && $totalSubmissions >= 5) return 'Good';
        if ($conversionRate >= 40 && $totalSubmissions >= 3) return 'Average';
        if ($totalSubmissions > 0) return 'Needs Improvement';
        return 'Inactive';
    }
    
    
    public function getCloserDetails(Request $request)
{
    try {
        $closerId = $request->get('closername');
        $closerName = $request->get('closer_name');
        $filter = $request->get('filter', 'monthly');
        $currentDate = Carbon::now('Asia/Karachi');
        
        if (!$closerId && !$closerName) {
            return response()->json(['error' => 'Closer ID or name required'], 400);
        }
        
        // ✅ PRIORITY 1: Handle specific date
        if ($request->filled('specific_date')) {
            $specificDate = Carbon::parse($request->specific_date, 'Asia/Karachi');
            $adjustedDate = $specificDate->copy()->subHours(12);
            $startDate = $adjustedDate->copy()->startOfDay()->addHours(12)->utc();
            $endDate = $adjustedDate->copy()->addDay()->startOfDay()->addHours(12)->subMinute()->utc();
        }
        // ✅ PRIORITY 2: Handle month picker
        elseif ($request->filled('month_year')) {
            [$year, $month] = explode('-', $request->month_year);
            $startDate = Carbon::createFromDate($year, $month, 1, 'Asia/Karachi')->startOfMonth()->utc();
            $endDate = Carbon::createFromDate($year, $month, 1, 'Asia/Karachi')->endOfMonth()->utc();
        }
        // ✅ PRIORITY 3: Regular filters
        else {
            switch ($filter) {
                case 'daily':
                    $adjustedDate = $currentDate->copy()->subHours(12);
                    $startDate = $adjustedDate->copy()->startOfDay()->addHours(12)->utc();
                    $endDate = $adjustedDate->copy()->addDay()->startOfDay()->addHours(12)->subMinute()->utc();
                    break;
                case 'weekly':
                    $startDate = $currentDate->copy()->startOfWeek()->utc();
                    $endDate = $currentDate->copy()->endOfWeek()->utc();
                    break;
                default:
                    $startDate = $currentDate->copy()->startOfMonth()->utc();
                    $endDate = $currentDate->copy()->endOfMonth()->utc();
                    break;
            }
        }
        
        // Build query for closer details
        $query = ClosedCall::whereBetween('created_at', [$startDate, $endDate]);
        
        if ($closerId) {
            $query->where('closername', $closerId);
        } else {
            $query->where('closername', $closerName);
        }
        
        // Get detailed submissions for this closer
        $submissions = $query->orderBy('created_at', 'desc')
            ->get(['id', 'customer_full_name', 'status', 'created_at', 'center_name', 'clients_id', 'monthly_premium']);
        
        // Calculate premium statistics for this closer
        $premiumQuery = ClosedCall::whereBetween('created_at', [$startDate, $endDate]);
        if ($closerId) {
            $premiumQuery->where('closername', $closerId);
        } else {
            $premiumQuery->where('closername', $closerName);
        }
        
        $premiumStats = $premiumQuery->whereNotNull('monthly_premium')
            ->where('monthly_premium', '>', 0)
            ->selectRaw('
                AVG(monthly_premium) as avg_premium,
                SUM(monthly_premium) as total_premium,
                MIN(monthly_premium) as min_premium,
                MAX(monthly_premium) as max_premium,
                COUNT(*) as premium_count
            ')
            ->first();
        
        // Get closer info
        $closer = null;
        if ($closerId) {
            $closer = User::find($closerId);
        }
        
        // Get stats
        $stats = [
            'total_submissions' => $submissions->count(),
            'approved_count' => $submissions->whereIn('status', self::APPROVED_STATUSES)->count(),
            'pending_count' => $submissions->whereNotIn('status', self::APPROVED_STATUSES)->count(),
            'conversion_rate' => $submissions->count() > 0 
                ? round(($submissions->whereIn('status', self::APPROVED_STATUSES)->count() / $submissions->count()) * 100, 2) 
                : 0,
            'avg_premium' => round($premiumStats->avg_premium ?? 0, 2),
            'total_premium' => round($premiumStats->total_premium ?? 0, 2),
            'min_premium' => round($premiumStats->min_premium ?? 0, 2),
            'max_premium' => round($premiumStats->max_premium ?? 0, 2),
            'yearly_estimate' => round(($premiumStats->total_premium ?? 0) * 12, 2),
            'premium_submissions' => $premiumStats->premium_count ?? 0
        ];
        
        return response()->json([
            'closer' => [
                'id' => $closerId,
                'name' => $closer ? $closer->name : $closerName,
                'email' => $closer ? $closer->email : '',
                'designation' => $this->getDesignation($closer)
            ],
            'stats' => $stats,
            'submissions' => $submissions,
            'period' => [
                'filter' => $filter,
                'start_date' => $startDate->format('Y-m-d H:i:s'),
                'end_date' => $endDate->format('Y-m-d H:i:s')
            ]
        ]);
        
    } catch (\Exception $e) {
        Log::error('Closer details error: ' . $e->getMessage());
        return response()->json(['error' => $e->getMessage()], 500);
    }
}
    
   
public function exportCloserReport(Request $request)
{
    try {
        $filter = $request->get('filter', 'monthly');
        $currentDate = Carbon::now('Asia/Karachi');
        
        // ✅ PRIORITY 1: Handle specific date
        if ($request->filled('specific_date')) {
            $specificDate = Carbon::parse($request->specific_date, 'Asia/Karachi');
            $adjustedDate = $specificDate->copy()->subHours(12);
            $startDate = $adjustedDate->copy()->startOfDay()->addHours(12)->utc();
            $endDate = $adjustedDate->copy()->addDay()->startOfDay()->addHours(12)->subMinute()->utc();
            $period = $specificDate->format('F_d_Y');
        }
        // ✅ PRIORITY 2: Handle month picker
        elseif ($request->filled('month_year')) {
            [$year, $month] = explode('-', $request->month_year);
            $startDate = Carbon::createFromDate($year, $month, 1, 'Asia/Karachi')->startOfMonth()->utc();
            $endDate = Carbon::createFromDate($year, $month, 1, 'Asia/Karachi')->endOfMonth()->utc();
            $period = Carbon::createFromDate($year, $month, 1)->format('F_Y');
        }
        // ✅ PRIORITY 3: Regular filters
        else {
            switch ($filter) {
                case 'daily':
                    $adjustedDate = $currentDate->copy()->subHours(12);
                    $startDate = $adjustedDate->copy()->startOfDay()->addHours(12)->utc();
                    $endDate = $adjustedDate->copy()->addDay()->startOfDay()->addHours(12)->subMinute()->utc();
                    $period = 'Daily';
                    break;
                case 'weekly':
                    $startDate = $currentDate->copy()->startOfWeek()->utc();
                    $endDate = $currentDate->copy()->endOfWeek()->utc();
                    $period = 'Weekly';
                    break;
                default:
                    $startDate = $currentDate->copy()->startOfMonth()->utc();
                    $endDate = $currentDate->copy()->endOfMonth()->utc();
                    $period = 'Monthly';
                    break;
            }
        }
        
        $closerReports = $this->getCloserReports($startDate, $endDate);
        
        $filename = "closer_report_{$period}_" . $currentDate->format('Y_m_d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        
        $callback = function() use ($closerReports, $period, $startDate, $endDate) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, ["Closer Performance Report - $period"]);
            fputcsv($file, ["Period: " . $startDate->format('Y-m-d') . " to " . $endDate->format('Y-m-d')]);
            fputcsv($file, ["Generated: " . now()->format('Y-m-d H:i:s')]);
            fputcsv($file, []);
            
            fputcsv($file, [
                'Closer ID',
                'Closer Name', 
                'Designation',
                'Company/Center',
                'Total Submissions',
                'Approved/Sales',
                'Pending',
                'Conversion Rate (%)',
                'Average Premium ($)',
                'Yearly Estimate ($)',
                'Performance Rating'
            ]);
            
            foreach ($closerReports as $report) {
                fputcsv($file, [
                    $report->closername ?: 'N/A',
                    $report->closer_name,
                    $report->designation,
                    $report->company,
                    $report->total_submissions,
                    $report->approved_count,
                    $report->pending_count,
                    $report->conversion_rate,
                    $report->avg_premium,
                    $report->yearly_premium_estimate,
                    $report->performance_rating
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
        
    } catch (\Exception $e) {
        Log::error('Closer export error: ' . $e->getMessage());
        return response()->json(['error' => 'Export failed: ' . $e->getMessage()], 500);
    }
}

     private function getDetailedCloserData($startDate, $endDate)
    {
        try {
            $approvedCountSql = 'SUM(CASE WHEN status IN ("' . implode('","', self::APPROVED_STATUSES) . '") THEN 1 ELSE 0 END)';
            $pendingCountSql = 'SUM(CASE WHEN status IN ("' . implode('","', self::PENDING_STATUSES) . '") THEN 1 ELSE 0 END)';
            $rejectedCountSql = 'SUM(CASE WHEN status IN ("' . implode('","', self::REJECTED_STATUSES) . '") THEN 1 ELSE 0 END)';
            $conversionRateSql = 'ROUND((' . $approvedCountSql . ' / COUNT(*)) * 100, 2)';
            
            // Get ALL clients from users table where type = 'client'
            $allClients = User::where('type', 'client')
                ->select('id', 'name')
                ->orderBy('name')
                ->get();
            
            // Get ALL carriers that have submissions in this period (only active carriers)
            $activeCarriers = ClosedCall::whereBetween('created_at', [$startDate, $endDate])
                ->whereNotNull('carrier')
                ->where('carrier', '!=', '')
                ->distinct()
                ->pluck('carrier')
                ->filter()
                ->sort()
                ->values()
                ->toArray();
            
            // Get ALL closers with COMPLETE status breakdown
            $closersWithId = ClosedCall::select(
                    'closername',
                    'center_name',
                    DB::raw('MAX(closername) as closername'),
                    DB::raw('COUNT(*) as total_submissions'),
                    DB::raw($approvedCountSql . ' as approved_count'),
                    DB::raw($pendingCountSql . ' as pending_count'),
                    DB::raw($rejectedCountSql . ' as rejected_count'),
                    DB::raw($conversionRateSql . ' as conversion_rate'),
                    // Calculate Level/GI counts ONLY from approved records
                    DB::raw('SUM(CASE WHEN status IN ("' . implode('","', self::APPROVED_STATUSES) . '") AND customer_eligibility IN ("Level", "Graded" , "Modified", "Standard", "Modified" ,"Preferred", "Senior choice immediate", "Golden solution immediate", "Senior choice graded", "Golden solution graded", "Senior choice rop", "Golden solution rop", "Express select", "ROP") THEN 1 ELSE 0 END) as level_count'),
                    DB::raw('SUM(CASE WHEN status IN ("' . implode('","', self::APPROVED_STATUSES) . '") AND customer_eligibility IN ("Guaranteed Issue" , "Graded GTL") THEN 1 ELSE 0 END) as gi_count'),
                    DB::raw('ROUND(AVG(CASE WHEN monthly_premium IS NOT NULL AND monthly_premium > 0 THEN monthly_premium END), 2) as avg_premium'),
                    DB::raw('SUM(CASE WHEN monthly_premium IS NOT NULL AND monthly_premium > 0 THEN monthly_premium ELSE 0 END) as total_premium')
                )
                ->whereBetween('created_at', [$startDate, $endDate])
                ->whereNotNull('closername')
                ->groupBy('closername', 'center_name')
                ->get();

            // Get external closers with COMPLETE status breakdown
            $closersWithoutId = ClosedCall::select(
                    DB::raw('NULL as closername'),
                    'closername',
                    'center_name',
                    DB::raw('COUNT(*) as total_submissions'),
                    DB::raw($approvedCountSql . ' as approved_count'),
                    DB::raw($pendingCountSql . ' as pending_count'),
                    DB::raw($rejectedCountSql . ' as rejected_count'),
                    DB::raw($conversionRateSql . ' as conversion_rate'),
                    // Calculate Level/GI counts ONLY from approved records
                    DB::raw('SUM(CASE WHEN status IN ("' . implode('","', self::APPROVED_STATUSES) . '") AND customer_eligibility IN ("Level", "Graded" , "Modified", "Standard", "Modified" ,"Preferred", "Senior choice immediate", "Golden solution immediate", "Senior choice graded", "Golden solution graded", "Senior choice rop", "Golden solution rop", "Express select", "ROP") THEN 1 ELSE 0 END) as level_count'),
                    DB::raw('SUM(CASE WHEN status IN ("' . implode('","', self::APPROVED_STATUSES) . '") AND customer_eligibility IN ("Guaranteed Issue" , "Graded GTL") THEN 1 ELSE 0 END) as gi_count'),
                    DB::raw('ROUND(AVG(CASE WHEN monthly_premium IS NOT NULL AND monthly_premium > 0 THEN monthly_premium END), 2) as avg_premium'),
                    DB::raw('SUM(CASE WHEN monthly_premium IS NOT NULL AND monthly_premium > 0 THEN monthly_premium ELSE 0 END) as total_premium')
                )
                ->whereBetween('created_at', [$startDate, $endDate])
                ->whereNull('closername')
                ->whereNotNull('closername')
                ->groupBy('closername', 'center_name')
                ->get();

            // Combine all closers
            $allClosers = $closersWithId->concat($closersWithoutId);

            // Build detailed reports with COMPLETE client data INCLUDING carrier breakdown
            $closerReports = collect();
            
            foreach ($allClosers as $closer) {
                // Get closer user information
                if ($closer->closername) {
                    $closerUser = User::find($closer->closername);
                    $closerName = $closerUser ? $closerUser->name : ($closer->closername ?: 'Unknown');
                    $closerEmail = $closerUser ? $closerUser->email : '';
                } else {
                    $closerName = $closer->closername ?: 'Unknown';
                    $closerEmail = '';
                }
                
                // Calculate overall percentages based on APPROVED records only
                $levelPercent = $closer->approved_count > 0 ? round(($closer->level_count / $closer->approved_count) * 100) : 0;
                $giPercent = $closer->approved_count > 0 ? round(($closer->gi_count / $closer->approved_count) * 100) : 0;
                
                // Calculate additional percentages
                $pendingPercent = $closer->total_submissions > 0 ? round(($closer->pending_count / $closer->total_submissions) * 100, 1) : 0;
                $rejectedPercent = $closer->total_submissions > 0 ? round(($closer->rejected_count / $closer->total_submissions) * 100, 1) : 0;
                
                // Create base report object with ALL status counts
                $report = (object) [
                    'closername' => $closer->closername,
                    'closer_name' => $closerName,
                    'closer_email' => $closerEmail,
                    'center_name' => $closer->center_name ?: 'Unknown',
                    'total_submissions' => $closer->total_submissions,
                    'approved_count' => $closer->approved_count,
                    'pending_count' => $closer->pending_count,
                    'rejected_count' => $closer->rejected_count,
                    'conversion_rate' => $closer->conversion_rate ?: 0,
                    'pending_percent' => $pendingPercent,
                    'rejected_percent' => $rejectedPercent,
                    'level_count' => $closer->level_count,
                    'gi_count' => $closer->gi_count,
                    'level_percent' => $levelPercent,
                    'gi_percent' => $giPercent,
                    'avg_premium' => $closer->avg_premium ?: 0,
                    'yearly_premium' => round(($closer->total_premium ?: 0) * 12, 2),
                    'client_data' => [] // This will hold COMPLETE data for each client INCLUDING carriers
                ];
                
                // Get COMPLETE data for EACH client with CARRIER breakdown
                foreach ($allClients as $client) {
                    // Get data for this specific client and closer combination
                    $query = ClosedCall::whereBetween('created_at', [$startDate, $endDate])
                        ->where('clients_id', $client->id);
                    
                    if ($closer->closername) {
                        $query->where('closername', $closer->closername);
                        if ($closer->center_name) {
                            $query->where('center_name', $closer->center_name);
                        }
                    } else {
                        $query->where('closername', $closer->closername)
                              ->where('center_name', $closer->center_name);
                    }
                    
                    $clientData = $query->selectRaw('
                        COUNT(*) as submissions,
                        SUM(CASE WHEN status IN ("' . implode('","', self::APPROVED_STATUSES) . '") THEN 1 ELSE 0 END) as approved,
                        SUM(CASE WHEN status IN ("' . implode('","', self::PENDING_STATUSES) . '") THEN 1 ELSE 0 END) as pending,
                        SUM(CASE WHEN status IN ("' . implode('","', self::REJECTED_STATUSES) . '") THEN 1 ELSE 0 END) as rejected,
                        SUM(CASE WHEN status IN ("' . implode('","', self::APPROVED_STATUSES) . '") AND customer_eligibility IN ("Level", "Graded" , "Modified", "Standard", "Modified" ,"Preferred", "Senior choice immediate", "Golden solution immediate", "Senior choice graded", "Golden solution graded", "Senior choice rop", "Golden solution rop", "Express select", "ROP") THEN 1 ELSE 0 END) as level_approved,
                        SUM(CASE WHEN status IN ("' . implode('","', self::APPROVED_STATUSES) . '") AND customer_eligibility IN ("Guaranteed Issue" , "Graded GTL") THEN 1 ELSE 0 END) as gi_approved
                    ')->first();
                    
                    // Get CARRIER breakdown for this client-closer combination
                    $carrierBreakdown = [];
                    if ($clientData && $clientData->submissions > 0) {
                        $carrierQuery = ClosedCall::whereBetween('created_at', [$startDate, $endDate])
                            ->where('clients_id', $client->id);
                        
                        if ($closer->closername) {
                            $carrierQuery->where('closername', $closer->closername);
                            if ($closer->center_name) {
                                $carrierQuery->where('center_name', $closer->center_name);
                            }
                        } else {
                            $carrierQuery->where('closername', $closer->closername)
                                      ->where('center_name', $closer->center_name);
                        }
                        
                        $carrierCounts = $carrierQuery
                            ->select('carrier', DB::raw('COUNT(*) as count'))
                            ->whereNotNull('carrier')
                            ->where('carrier', '!=', '')
                            ->groupBy('carrier')
                            ->get();
                        
                        foreach ($carrierCounts as $carrierCount) {
                            $carrierBreakdown[$carrierCount->carrier] = $carrierCount->count;
                        }
                    }
                    
                    // Ensure zeros are properly handled
                    $submissions = $clientData->submissions ?: 0;
                    $approved = $clientData->approved ?: 0;
                    $pending = $clientData->pending ?: 0;
                    $rejected = $clientData->rejected ?: 0;
                    $levelApproved = $clientData->level_approved ?: 0;
                    $giApproved = $clientData->gi_approved ?: 0;
                    
                    // Calculate percentages
                    $approvedPercent = $submissions > 0 ? round(($approved / $submissions) * 100, 1) : 0;
                    $pendingPercent = $submissions > 0 ? round(($pending / $submissions) * 100, 1) : 0;
                    $rejectedPercent = $submissions > 0 ? round(($rejected / $submissions) * 100, 1) : 0;
                    $levelPercent = $approved > 0 ? round(($levelApproved / $approved) * 100) : 0;
                    $giPercent = $approved > 0 ? round(($giApproved / $approved) * 100) : 0;
                    
                    // Store COMPLETE client data with CARRIER breakdown
                    $report->client_data[$client->id] = (object) [
                        'client_id' => $client->id,
                        'client_name' => $client->name,
                        'submissions' => $submissions,
                        'approved' => $approved,
                        'pending' => $pending,
                        'rejected' => $rejected,
                        'level_approved' => $levelApproved,
                        'gi_approved' => $giApproved,
                        'approved_percent' => $approvedPercent,
                        'pending_percent' => $pendingPercent,
                        'rejected_percent' => $rejectedPercent,
                        'level_percent' => $levelPercent,
                        'gi_percent' => $giPercent,
                        'carrier_breakdown' => $carrierBreakdown // NEW: Carrier data
                    ];
                }
                
                $closerReports->push($report);
            }

            // Sort by approved count (highest first)
            $sortedReports = $closerReports->sortByDesc('approved_count')->values();
            
            return (object) [
                'closerReports' => $sortedReports,
                'allClients' => $allClients,
                'activeCarriers' => $activeCarriers // NEW: Return active carriers list
            ];
            
        } catch (\Exception $e) {
            Log::error('Detailed closer reports error: ' . $e->getMessage());
            return (object) [
                'closerReports' => collect(),
                'allClients' => collect(),
                'activeCarriers' => [] // NEW: Return empty array on error
            ];
        }
    }

    // UPDATED: Main controller method to pass active carriers
    public function detailedCloserReport(Request $request)
    {
        $filter = $request->get('filter', 'monthly');
        $currentDate = Carbon::now('Asia/Karachi');
        
        // Handle custom date range
        if ($filter === 'custom' && $request->has('start_date') && $request->has('end_date')) {
            $startDate = Carbon::parse($request->get('start_date'), 'Asia/Karachi')->startOfDay()->utc();
            $endDate = Carbon::parse($request->get('end_date'), 'Asia/Karachi')->endOfDay()->utc();
            $period = Carbon::parse($request->get('start_date'))->format('M d, Y') . ' - ' . Carbon::parse($request->get('end_date'))->format('M d, Y');
        } else {
            // Define date ranges based on filter
            switch ($filter) {
                case 'daily':
                    $adjustedDate = $currentDate->copy()->subHours(12);
                    $startDate = $adjustedDate->copy()->startOfDay()->addHours(12)->utc();
                    $endDate = $adjustedDate->copy()->addDay()->startOfDay()->addHours(12)->subMinute()->utc();
                    $period = 'Today';
                    break;
                case 'weekly':
                    $startDate = $currentDate->copy()->startOfWeek()->utc();
                    $endDate = $currentDate->copy()->endOfWeek()->utc();
                    $period = 'This Week';
                    break;
                default: // monthly
                    $startDate = $currentDate->copy()->startOfMonth()->utc();
                    $endDate = $currentDate->copy()->endOfMonth()->utc();
                    $period = 'This Month';
                    break;
            }
        }

        // Get detailed closer reports with client data INCLUDING carrier breakdown
        $reportData = $this->getDetailedCloserData($startDate, $endDate);
        $closerReports = $reportData->closerReports;
        $allClients = $reportData->allClients;
        $activeCarriers = $reportData->activeCarriers; // NEW: Get active carriers
        
        return view('sales.closer-details', compact(
            'closerReports',
            'allClients',
            'activeCarriers', // NEW: Pass active carriers to view
            'filter', 
            'period',
            'startDate',
            'endDate'
        ));
    }

    // UPDATED: Export method with carrier breakdown
    public function exportDetailedCloserReport(Request $request)
    {
        try {
            $filter = $request->get('filter', 'monthly');
            $currentDate = Carbon::now('Asia/Karachi');
            
            // Handle date ranges (same as before)
            if ($filter === 'custom' && $request->has('start_date') && $request->has('end_date')) {
                $startDate = Carbon::parse($request->get('start_date'), 'Asia/Karachi')->startOfDay()->utc();
                $endDate = Carbon::parse($request->get('end_date'), 'Asia/Karachi')->endOfDay()->utc();
                $period = Carbon::parse($request->get('start_date'))->format('M_d_Y') . '_to_' . Carbon::parse($request->get('end_date'))->format('M_d_Y');
            } else {
                switch ($filter) {
                    case 'daily':
                        $adjustedDate = $currentDate->copy()->subHours(12);
                        $startDate = $adjustedDate->copy()->startOfDay()->addHours(12)->utc();
                        $endDate = $adjustedDate->copy()->addDay()->startOfDay()->addHours(12)->subMinute()->utc();
                        $period = 'Daily';
                        break;
                    case 'weekly':
                        $startDate = $currentDate->copy()->startOfWeek()->utc();
                        $endDate = $currentDate->copy()->endOfWeek()->utc();
                        $period = 'Weekly';
                        break;
                    default:
                        $startDate = $currentDate->copy()->startOfMonth()->utc();
                        $endDate = $currentDate->copy()->endOfMonth()->utc();
                        $period = 'Monthly';
                        break;
                }
            }
            
            // Get detailed closer reports with COMPLETE client data INCLUDING carriers
            $reportData = $this->getDetailedCloserData($startDate, $endDate);
            $closerReports = $reportData->closerReports;
            $allClients = $reportData->allClients;
            $activeCarriers = $reportData->activeCarriers;
            
            $filename = "complete_closer_report_with_carriers_{$filter}_" . $currentDate->format('Y_m_d_H_i_s') . '.csv';
            
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"$filename\"",
            ];
            
            $callback = function() use ($closerReports, $allClients, $activeCarriers, $period, $startDate, $endDate) {
                $file = fopen('php://output', 'w');
                
                // Add header info
                fputcsv($file, ["COMPLETE Closer Performance Report with Client Status + Carrier Breakdown"]);
                fputcsv($file, ["Period: " . $startDate->format('Y-m-d H:i') . " to " . $endDate->format('Y-m-d H:i')]);
                fputcsv($file, ["Generated: " . now()->format('Y-m-d H:i:s')]);
                fputcsv($file, ["Total Closers: " . $closerReports->count()]);
                fputcsv($file, ["Total Clients: " . $allClients->count()]);
                fputcsv($file, ["Active Carriers: " . count($activeCarriers)]);
                fputcsv($file, ["Status Categories: Submissions, Approved, Pending, Rejected, Level (App), GI (App) + Carrier Breakdown"]);
                fputcsv($file, []); // Empty row
                
                // Build COMPLETE dynamic headers INCLUDING carrier columns
                $headers = [
                    'Closer Name', 'Email', 'Center',
                    'Total Submissions', 'Total Approved', 'Total Pending', 'Total Rejected',
                    'Total Level (Approved)', 'Total GI (Approved)',
                    'Conversion Rate (%)', 'Pending Rate (%)', 'Rejected Rate (%)',
                    'Yearly Premium ($)', 'Average Premium ($)',
                    'GI Percentage (%) - Approved Only', 'Level Percentage (%) - Approved Only',
                    '' // Separator column before client data
                ];
                
                // Add COMPLETE client-specific headers (8 status columns + carrier columns per client)
                foreach ($allClients as $index => $client) {
                    if ($index > 0) {
                        $headers[] = ''; // Separator column between clients
                    }
                    // Status columns
                    $headers[] = $client->name . ' - Sub';
                    $headers[] = $client->name . ' - App';
                    $headers[] = $client->name . ' - Pen';
                    $headers[] = $client->name . ' - Rej';
                    $headers[] = $client->name . ' - Lv (App)';
                    $headers[] = $client->name . ' - GI (App)';
                    $headers[] = $client->name . ' - App%';
                    $headers[] = $client->name . ' - Pen%';
                    
                    // Carrier columns (only active carriers)
                    foreach ($activeCarriers as $carrier) {
                        $headers[] = $client->name . ' - ' . $carrier;
                    }
                }
                
                fputcsv($file, $headers);
                
                // Add visual separator row
                $separatorRow = array_fill(0, count($headers), '---');
                fputcsv($file, $separatorRow);
                
                // Add data rows with COMPLETE status + carrier breakdown
                foreach ($closerReports as $report) {
                    $row = [
                        $report->closer_name, $report->closer_email, $report->center_name,
                        $report->total_submissions, $report->approved_count, $report->pending_count, $report->rejected_count,
                        $report->level_count, $report->gi_count,
                        $report->conversion_rate, $report->pending_percent, $report->rejected_percent,
                        $report->yearly_premium, $report->avg_premium,
                        $report->gi_percent, $report->level_percent,
                        '|' // Visual separator before client data
                    ];
                    
                    // Add COMPLETE client-specific data INCLUDING carriers
                    foreach ($allClients as $index => $client) {
                        if ($index > 0) {
                            $row[] = '|'; // Visual separator between clients
                        }
                        
                        $clientData = isset($report->client_data[$client->id]) ? $report->client_data[$client->id] : null;
                        
                        // Status data
                        $row[] = $clientData ? $clientData->submissions : 0;
                        $row[] = $clientData ? $clientData->approved : 0;
                        $row[] = $clientData ? $clientData->pending : 0;
                        $row[] = $clientData ? $clientData->rejected : 0;
                        $row[] = $clientData ? $clientData->level_approved : 0;
                        $row[] = $clientData ? $clientData->gi_approved : 0;
                        $row[] = $clientData ? $clientData->approved_percent : 0;
                        $row[] = $clientData ? $clientData->pending_percent : 0;
                        
                        // Carrier data
                        foreach ($activeCarriers as $carrier) {
                            $carrierCount = 0;
                            if ($clientData && isset($clientData->carrier_breakdown[$carrier])) {
                                $carrierCount = $clientData->carrier_breakdown[$carrier];
                            }
                            $row[] = $carrierCount;
                        }
                    }
                    
                    fputcsv($file, $row);
                }
                
                // Add footer with carrier breakdown legend
                fputcsv($file, []);
                fputcsv($file, ["COMPLETE CLIENT + CARRIER BREAKDOWN LEGEND:"]);
                fputcsv($file, ["Sub = Submissions, App = Approved, Pen = Pending, Rej = Rejected"]);
                fputcsv($file, ["Lv = Level (Approved Only), GI = Guaranteed Issue (Approved Only)"]);
                fputcsv($file, ["App% = Approval Rate, Pen% = Pending Rate"]);
                fputcsv($file, ["Carrier columns show submission count per carrier per client"]);
                fputcsv($file, ["Active Carriers (" . count($activeCarriers) . "): " . implode(', ', $activeCarriers)]);
                
                fclose($file);
            };
            
            return response()->stream($callback, 200, $headers);
            
        } catch (\Exception $e) {
            Log::error('Detailed closer export with carriers error: ' . $e->getMessage());
            return response()->json(['error' => 'Export failed: ' . $e->getMessage()], 500);
        }
    }



private function getDetailedJuniorCloserData($startDate, $endDate)
{
    try {
        $approvedCountSql = 'SUM(CASE WHEN status IN ("' . implode('","', self::APPROVED_STATUSES) . '") THEN 1 ELSE 0 END)';
        $pendingCountSql = 'SUM(CASE WHEN status IN ("' . implode('","', self::PENDING_STATUSES) . '") THEN 1 ELSE 0 END)';
        $rejectedCountSql = 'SUM(CASE WHEN status IN ("' . implode('","', self::REJECTED_STATUSES) . '") THEN 1 ELSE 0 END)';
        $conversionRateSql = 'ROUND((' . $approvedCountSql . ' / COUNT(*)) * 100, 2)';
        
        // Get ALL clients from users table where type = 'client'
        $allClients = User::where('type', 'client')
            ->select('id', 'name')
            ->orderBy('name')
            ->get();
        
        // Get ALL carriers that have submissions in this period (only active carriers)
        $activeCarriers = ClosedCall::whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('carrier')
            ->where('carrier', '!=', '')
            ->distinct()
            ->pluck('carrier')
            ->filter()
            ->sort()
            ->values()
            ->toArray();
        
        // Get ALL junior closers with COMPLETE status breakdown (ONLY NUMERIC IDs)
        $juniorClosers = ClosedCall::select(
                'junior_closer_name',
                'center_name',
                DB::raw('COUNT(*) as total_submissions'),
                DB::raw($approvedCountSql . ' as approved_count'),
                DB::raw($pendingCountSql . ' as pending_count'),
                DB::raw($rejectedCountSql . ' as rejected_count'),
                DB::raw($conversionRateSql . ' as conversion_rate'),
                // Calculate Level/GI counts ONLY from approved records
                DB::raw('SUM(CASE WHEN status IN ("' . implode('","', self::APPROVED_STATUSES) . '") AND customer_eligibility IN ("Level", "Graded" , "Modified", "Standard", "Modified" ,"Preferred", "Senior choice immediate", "Golden solution immediate", "Senior choice graded", "Golden solution graded", "Senior choice rop", "Golden solution rop", "Express select", "ROP") THEN 1 ELSE 0 END) as level_count'),
                DB::raw('SUM(CASE WHEN status IN ("' . implode('","', self::APPROVED_STATUSES) . '") AND customer_eligibility IN ("Guaranteed Issue" , "Graded GTL") THEN 1 ELSE 0 END) as gi_count'),
                DB::raw('ROUND(AVG(CASE WHEN monthly_premium IS NOT NULL AND monthly_premium > 0 THEN monthly_premium END), 2) as avg_premium'),
                DB::raw('SUM(CASE WHEN monthly_premium IS NOT NULL AND monthly_premium > 0 THEN monthly_premium ELSE 0 END) as total_premium')
            )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('junior_closer_name')
            ->where('junior_closer_name', '!=', '')
            ->whereRaw('junior_closer_name REGEXP "^[0-9]+$"') // Only numeric IDs
            ->groupBy('junior_closer_name', 'center_name')
            ->get();

        // Build detailed reports with COMPLETE client data INCLUDING carrier breakdown
        $juniorCloserReports = collect();
        
        foreach ($juniorClosers as $juniorCloser) {
            // Get junior closer user information using the NUMERIC ID stored in junior_closer_name
            $juniorCloserUser = null;
            $juniorCloserName = 'Unknown';
            $juniorCloserEmail = '';
            
            // Only try to find user if junior_closer_name is numeric
            if (is_numeric($juniorCloser->junior_closer_name)) {
                $juniorCloserUser = User::find($juniorCloser->junior_closer_name);
                if ($juniorCloserUser) {
                    $juniorCloserName = $juniorCloserUser->name;
                    $juniorCloserEmail = $juniorCloserUser->email;
                }
            }
            
            // Calculate overall percentages based on APPROVED records only
            $levelPercent = $juniorCloser->approved_count > 0 ? round(($juniorCloser->level_count / $juniorCloser->approved_count) * 100) : 0;
            $giPercent = $juniorCloser->approved_count > 0 ? round(($juniorCloser->gi_count / $juniorCloser->approved_count) * 100) : 0;
            
            // Calculate additional percentages
            $pendingPercent = $juniorCloser->total_submissions > 0 ? round(($juniorCloser->pending_count / $juniorCloser->total_submissions) * 100, 1) : 0;
            $rejectedPercent = $juniorCloser->total_submissions > 0 ? round(($juniorCloser->rejected_count / $juniorCloser->total_submissions) * 100, 1) : 0;
            
            // Create base report object with ALL status counts
            $report = (object) [
                'junior_closername' => $juniorCloser->junior_closer_name,
                'junior_closer_name' => $juniorCloserName,
                'junior_closer_email' => $juniorCloserEmail,
                'center_name' => $juniorCloser->center_name ?: 'Unknown',
                'total_submissions' => $juniorCloser->total_submissions,
                'approved_count' => $juniorCloser->approved_count,
                'pending_count' => $juniorCloser->pending_count,
                'rejected_count' => $juniorCloser->rejected_count,
                'conversion_rate' => $juniorCloser->conversion_rate ?: 0,
                'pending_percent' => $pendingPercent,
                'rejected_percent' => $rejectedPercent,
                'level_count' => $juniorCloser->level_count,
                'gi_count' => $juniorCloser->gi_count,
                'level_percent' => $levelPercent,
                'gi_percent' => $giPercent,
                'avg_premium' => $juniorCloser->avg_premium ?: 0,
                'yearly_premium' => round(($juniorCloser->total_premium ?: 0) * 12, 2),
                'client_data' => [] // This will hold COMPLETE data for each client INCLUDING carriers
            ];
            
            // Get COMPLETE data for EACH client with CARRIER breakdown
            foreach ($allClients as $client) {
                // Get data for this specific client and junior closer combination (NUMERIC ID ONLY)
                $query = ClosedCall::whereBetween('created_at', [$startDate, $endDate])
                    ->where('clients_id', $client->id)
                    ->where('junior_closer_name', $juniorCloser->junior_closer_name)
                    ->where('center_name', $juniorCloser->center_name)
                    ->whereRaw('junior_closer_name REGEXP "^[0-9]+$"'); // Ensure numeric ID
                
                $clientData = $query->selectRaw('
                    COUNT(*) as submissions,
                    SUM(CASE WHEN status IN ("' . implode('","', self::APPROVED_STATUSES) . '") THEN 1 ELSE 0 END) as approved,
                    SUM(CASE WHEN status IN ("' . implode('","', self::PENDING_STATUSES) . '") THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN status IN ("' . implode('","', self::REJECTED_STATUSES) . '") THEN 1 ELSE 0 END) as rejected,
                    SUM(CASE WHEN status IN ("' . implode('","', self::APPROVED_STATUSES) . '") AND customer_eligibility IN ("Level", "Graded" , "Modified", "Standard", "Modified" ,"Preferred", "Senior choice immediate", "Golden solution immediate", "Senior choice graded", "Golden solution graded", "Senior choice rop", "Golden solution rop", "Express select", "ROP") THEN 1 ELSE 0 END) as level_approved,
                    SUM(CASE WHEN status IN ("' . implode('","', self::APPROVED_STATUSES) . '") AND customer_eligibility IN ("Guaranteed Issue" , "Graded GTL") THEN 1 ELSE 0 END) as gi_approved
                ')->first();
                
                // Get CARRIER breakdown for this client-junior closer combination (NUMERIC ID ONLY)
                $carrierBreakdown = [];
                if ($clientData && $clientData->submissions > 0) {
                    $carrierQuery = ClosedCall::whereBetween('created_at', [$startDate, $endDate])
                        ->where('clients_id', $client->id)
                        ->where('junior_closer_name', $juniorCloser->junior_closer_name)
                        ->where('center_name', $juniorCloser->center_name)
                        ->whereRaw('junior_closer_name REGEXP "^[0-9]+$"'); // Ensure numeric ID
                    
                    $carrierCounts = $carrierQuery
                        ->select('carrier', DB::raw('COUNT(*) as count'))
                        ->whereNotNull('carrier')
                        ->where('carrier', '!=', '')
                        ->groupBy('carrier')
                        ->get();
                    
                    foreach ($carrierCounts as $carrierCount) {
                        $carrierBreakdown[$carrierCount->carrier] = $carrierCount->count;
                    }
                }
                
                // Ensure zeros are properly handled
                $submissions = $clientData->submissions ?: 0;
                $approved = $clientData->approved ?: 0;
                $pending = $clientData->pending ?: 0;
                $rejected = $clientData->rejected ?: 0;
                $levelApproved = $clientData->level_approved ?: 0;
                $giApproved = $clientData->gi_approved ?: 0;
                
                // Calculate percentages
                $approvedPercent = $submissions > 0 ? round(($approved / $submissions) * 100, 1) : 0;
                $pendingPercent = $submissions > 0 ? round(($pending / $submissions) * 100, 1) : 0;
                $rejectedPercent = $submissions > 0 ? round(($rejected / $submissions) * 100, 1) : 0;
                $levelPercent = $approved > 0 ? round(($levelApproved / $approved) * 100) : 0;
                $giPercent = $approved > 0 ? round(($giApproved / $approved) * 100) : 0;
                
                // Store COMPLETE client data with CARRIER breakdown
                $report->client_data[$client->id] = (object) [
                    'client_id' => $client->id,
                    'client_name' => $client->name,
                    'submissions' => $submissions,
                    'approved' => $approved,
                    'pending' => $pending,
                    'rejected' => $rejected,
                    'level_approved' => $levelApproved,
                    'gi_approved' => $giApproved,
                    'approved_percent' => $approvedPercent,
                    'pending_percent' => $pendingPercent,
                    'rejected_percent' => $rejectedPercent,
                    'level_percent' => $levelPercent,
                    'gi_percent' => $giPercent,
                    'carrier_breakdown' => $carrierBreakdown // NEW: Carrier data
                ];
            }
            
            $juniorCloserReports->push($report);
        }

        // Sort by approved count (highest first)
        $sortedReports = $juniorCloserReports->sortByDesc('approved_count')->values();
        
        return (object) [
            'juniorCloserReports' => $sortedReports,
            'allClients' => $allClients,
            'activeCarriers' => $activeCarriers // NEW: Return active carriers list
        ];
        
    } catch (\Exception $e) {
        Log::error('Detailed junior closer reports error: ' . $e->getMessage());
        return (object) [
            'juniorCloserReports' => collect(),
            'allClients' => collect(),
            'activeCarriers' => [] // NEW: Return empty array on error
        ];
    }
}

// UPDATED: Main controller method for junior closers
public function detailedJuniorCloserReport(Request $request)
{
    $filter = $request->get('filter', 'monthly');
    $currentDate = Carbon::now('Asia/Karachi');
    
    // Handle custom date range
    if ($filter === 'custom' && $request->has('start_date') && $request->has('end_date')) {
        $startDate = Carbon::parse($request->get('start_date'), 'Asia/Karachi')->startOfDay()->utc();
        $endDate = Carbon::parse($request->get('end_date'), 'Asia/Karachi')->endOfDay()->utc();
        $period = Carbon::parse($request->get('start_date'))->format('M d, Y') . ' - ' . Carbon::parse($request->get('end_date'))->format('M d, Y');
    } else {
        // Define date ranges based on filter
        switch ($filter) {
            case 'daily':
                $adjustedDate = $currentDate->copy()->subHours(12);
                $startDate = $adjustedDate->copy()->startOfDay()->addHours(12)->utc();
                $endDate = $adjustedDate->copy()->addDay()->startOfDay()->addHours(12)->subMinute()->utc();
                $period = 'Today';
                break;
            case 'weekly':
                $startDate = $currentDate->copy()->startOfWeek()->utc();
                $endDate = $currentDate->copy()->endOfWeek()->utc();
                $period = 'This Week';
                break;
            default: // monthly
                $startDate = $currentDate->copy()->startOfMonth()->utc();
                $endDate = $currentDate->copy()->endOfMonth()->utc();
                $period = 'This Month';
                break;
        }
    }

    // Get detailed junior closer reports with client data INCLUDING carrier breakdown
    $reportData = $this->getDetailedJuniorCloserData($startDate, $endDate);
    $juniorCloserReports = $reportData->juniorCloserReports;
    $allClients = $reportData->allClients;
    $activeCarriers = $reportData->activeCarriers; // NEW: Get active carriers
    
    return view('sales.junior-details', compact(
        'juniorCloserReports',
        'allClients',
        'activeCarriers', // NEW: Pass active carriers to view
        'filter', 
        'period',
        'startDate',
        'endDate'
    ));
}

// UPDATED: Export method for junior closers with carrier breakdown
public function exportDetailedJuniorCloserReport(Request $request)
{
    try {
        $filter = $request->get('filter', 'monthly');
        $currentDate = Carbon::now('Asia/Karachi');
        
        // Handle date ranges (same as before)
        if ($filter === 'custom' && $request->has('start_date') && $request->has('end_date')) {
            $startDate = Carbon::parse($request->get('start_date'), 'Asia/Karachi')->startOfDay()->utc();
            $endDate = Carbon::parse($request->get('end_date'), 'Asia/Karachi')->endOfDay()->utc();
            $period = Carbon::parse($request->get('start_date'))->format('M_d_Y') . '_to_' . Carbon::parse($request->get('end_date'))->format('M_d_Y');
        } else {
            switch ($filter) {
                case 'daily':
                    $adjustedDate = $currentDate->copy()->subHours(12);
                    $startDate = $adjustedDate->copy()->startOfDay()->addHours(12)->utc();
                    $endDate = $adjustedDate->copy()->addDay()->startOfDay()->addHours(12)->subMinute()->utc();
                    $period = 'Daily';
                    break;
                case 'weekly':
                    $startDate = $currentDate->copy()->startOfWeek()->utc();
                    $endDate = $currentDate->copy()->endOfWeek()->utc();
                    $period = 'Weekly';
                    break;
                default:
                    $startDate = $currentDate->copy()->startOfMonth()->utc();
                    $endDate = $currentDate->copy()->endOfMonth()->utc();
                    $period = 'Monthly';
                    break;
            }
        }
        
        // Get detailed junior closer reports with COMPLETE client data INCLUDING carriers
        $reportData = $this->getDetailedJuniorCloserData($startDate, $endDate);
        $juniorCloserReports = $reportData->juniorCloserReports;
        $allClients = $reportData->allClients;
        $activeCarriers = $reportData->activeCarriers;
        
        $filename = "complete_junior_closer_report_with_carriers_{$filter}_" . $currentDate->format('Y_m_d_H_i_s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        
        $callback = function() use ($juniorCloserReports, $allClients, $activeCarriers, $period, $startDate, $endDate) {
            $file = fopen('php://output', 'w');
            
            // Add header info
            fputcsv($file, ["COMPLETE Junior Closer Performance Report with Client Status + Carrier Breakdown"]);
            fputcsv($file, ["Period: " . $startDate->format('Y-m-d H:i') . " to " . $endDate->format('Y-m-d H:i')]);
            fputcsv($file, ["Generated: " . now()->format('Y-m-d H:i:s')]);
            fputcsv($file, ["Total Junior Closers: " . $juniorCloserReports->count()]);
            fputcsv($file, ["Total Clients: " . $allClients->count()]);
            fputcsv($file, ["Active Carriers: " . count($activeCarriers)]);
            fputcsv($file, ["Status Categories: Submissions, Approved, Pending, Rejected, Level (App), GI (App) + Carrier Breakdown"]);
            fputcsv($file, []); // Empty row
            
            // Build COMPLETE dynamic headers INCLUDING carrier columns
            $headers = [
                'Junior Closer Name', 'Email', 'Center',
                'Total Submissions', 'Total Approved', 'Total Pending', 'Total Rejected',
                'Total Level (Approved)', 'Total GI (Approved)',
                'Conversion Rate (%)', 'Pending Rate (%)', 'Rejected Rate (%)',
                'Yearly Premium ($)', 'Average Premium ($)',
                'GI Percentage (%) - Approved Only', 'Level Percentage (%) - Approved Only',
                '' // Separator column before client data
            ];
            
            // Add COMPLETE client-specific headers (8 status columns + carrier columns per client)
            foreach ($allClients as $index => $client) {
                if ($index > 0) {
                    $headers[] = ''; // Separator column between clients
                }
                // Status columns
                $headers[] = $client->name . ' - Sub';
                $headers[] = $client->name . ' - App';
                $headers[] = $client->name . ' - Pen';
                $headers[] = $client->name . ' - Rej';
                $headers[] = $client->name . ' - Lv (App)';
                $headers[] = $client->name . ' - GI (App)';
                $headers[] = $client->name . ' - App%';
                $headers[] = $client->name . ' - Pen%';
                
                // Carrier columns (only active carriers)
                foreach ($activeCarriers as $carrier) {
                    $headers[] = $client->name . ' - ' . $carrier;
                }
            }
            
            fputcsv($file, $headers);
            
            // Add visual separator row
            $separatorRow = array_fill(0, count($headers), '---');
            fputcsv($file, $separatorRow);
            
            // Add data rows with COMPLETE status + carrier breakdown
            foreach ($juniorCloserReports as $report) {
                $row = [
                    $report->junior_closer_name, $report->junior_closer_email, $report->center_name,
                    $report->total_submissions, $report->approved_count, $report->pending_count, $report->rejected_count,
                    $report->level_count, $report->gi_count,
                    $report->conversion_rate, $report->pending_percent, $report->rejected_percent,
                    $report->yearly_premium, $report->avg_premium,
                    $report->gi_percent, $report->level_percent,
                    '|' // Visual separator before client data
                ];
                
                // Add COMPLETE client-specific data INCLUDING carriers
                foreach ($allClients as $index => $client) {
                    if ($index > 0) {
                        $row[] = '|'; // Visual separator between clients
                    }
                    
                    $clientData = isset($report->client_data[$client->id]) ? $report->client_data[$client->id] : null;
                    
                    // Status data
                    $row[] = $clientData ? $clientData->submissions : 0;
                    $row[] = $clientData ? $clientData->approved : 0;
                    $row[] = $clientData ? $clientData->pending : 0;
                    $row[] = $clientData ? $clientData->rejected : 0;
                    $row[] = $clientData ? $clientData->level_approved : 0;
                    $row[] = $clientData ? $clientData->gi_approved : 0;
                    $row[] = $clientData ? $clientData->approved_percent : 0;
                    $row[] = $clientData ? $clientData->pending_percent : 0;
                    
                    // Carrier data
                    foreach ($activeCarriers as $carrier) {
                        $carrierCount = 0;
                        if ($clientData && isset($clientData->carrier_breakdown[$carrier])) {
                            $carrierCount = $clientData->carrier_breakdown[$carrier];
                        }
                        $row[] = $carrierCount;
                    }
                }
                
                fputcsv($file, $row);
            }
            
            // Add footer with carrier breakdown legend
            fputcsv($file, []);
            fputcsv($file, ["COMPLETE CLIENT + CARRIER BREAKDOWN LEGEND:"]);
            fputcsv($file, ["Sub = Submissions, App = Approved, Pen = Pending, Rej = Rejected"]);
            fputcsv($file, ["Lv = Level (Approved Only), GI = Guaranteed Issue (Approved Only)"]);
            fputcsv($file, ["App% = Approval Rate, Pen% = Pending Rate"]);
            fputcsv($file, ["Carrier columns show submission count per carrier per client"]);
            fputcsv($file, ["Active Carriers (" . count($activeCarriers) . "): " . implode(', ', $activeCarriers)]);
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
        
    } catch (\Exception $e) {
        Log::error('Detailed junior closer export with carriers error: ' . $e->getMessage());
        return response()->json(['error' => 'Export failed: ' . $e->getMessage()], 500);
    }
}

// NEW: Client's own reports page - shows only logged-in client's data
public function myReports(Request $request)
{
    $filter = $request->get('filter', 'monthly');
    $currentDate = Carbon::now('America/New_York'); // New York timezone

    // Get the logged-in client's ID
    $clientId = auth()->id();
    
    

    if ($request->filled('month_year')) {
        [$year, $month] = explode('-', $request->month_year);

        $startDate = Carbon::createFromDate($year, $month, 1, 'America/New_York')->startOfMonth()->utc();
        $endDate   = Carbon::createFromDate($year, $month, 1, 'America/New_York')->endOfMonth()->utc();
        $period = Carbon::createFromDate($year, $month, 1, 'America/New_York')->format('F Y');
        $filter = 'custom';
    } else {
        switch ($filter) {
            case 'daily':
                // Adjust for night shift - business day starts at 12:00 PM and ends at 11:59 AM next day
                $adjustedDate = $currentDate->copy()->subHours(12);
                $startDate = $adjustedDate->copy()->startOfDay()->addHours(12)->utc();
                $endDate = $adjustedDate->copy()->addDay()->startOfDay()->addHours(12)->subMinute()->utc();
                $period = 'Today';
                break;

            case 'weekly':
                $startDate = $currentDate->copy()->startOfWeek()->utc();
                $endDate = $currentDate->copy()->endOfWeek()->utc();
                $period = 'This Week';
                break;

            default:
                $startDate = $currentDate->copy()->startOfMonth()->utc();
                $endDate = $currentDate->copy()->endOfMonth()->utc();
                $period = 'This Month';
                break;
        }
    }

    // Get only this client's data
    $myData = $this->getMyClientData($clientId, $startDate, $endDate);
    $myCarrierCounts = $this->getMyCarrierCounts($clientId, $startDate, $endDate);
    $mySubmissions = $this->getMySubmissions($clientId, $startDate, $endDate);

    return view('sales.my-client-report', compact(
        'myData',
        'myCarrierCounts',
        'mySubmissions',
        'filter',
        'period',
        'startDate',
        'endDate'
    ));
}

// Get specific client's data only
private function getMyClientData($clientId, $startDate, $endDate)
{
    try {
        $approvedCountSql = 'SUM(CASE WHEN status IN ("' . implode('","', self::APPROVED_STATUSES) . '") THEN 1 ELSE 0 END)';
        $conversionRateSql = 'ROUND((' . $approvedCountSql . ' / COUNT(*)) * 100, 2)';
        $avgPremiumSql = 'ROUND(AVG(CASE WHEN status IN ("' . implode('","', self::APPROVED_STATUSES) . '") AND monthly_premium IS NOT NULL AND monthly_premium > 0 THEN monthly_premium END), 2)';
        $totalPremiumSql = 'SUM(CASE WHEN status IN ("' . implode('","', self::APPROVED_STATUSES) . '") AND monthly_premium IS NOT NULL AND monthly_premium > 0 THEN monthly_premium ELSE 0 END)';
        
        $data = ClosedCall::select(
                DB::raw('COUNT(*) as total_submissions'),
                DB::raw($approvedCountSql . ' as approved_count'),
                DB::raw($conversionRateSql . ' as conversion_rate'),
                DB::raw($avgPremiumSql . ' as avg_premium'),
                DB::raw($totalPremiumSql . ' as total_premium_volume')
            )
            ->where('clients_id', $clientId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->first();

        // FIXED: Calculate eligibility percentages (approved only)
        $levelCount = ClosedCall::where('clients_id', $clientId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('status', self::APPROVED_STATUSES)
            ->whereIn('customer_eligibility', [
                'Level', 'Graded', 'Modified', 'Standard', 'Preferred', 
                'Senior choice immediate', 'Golden solution immediate', 
                'Senior choice graded', 'Golden solution graded', 
                'Senior choice rop', 'Golden solution rop', 
                'Express select', 'ROP'
            ])
            ->count();
            
        $giCount = ClosedCall::where('clients_id', $clientId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('status', self::APPROVED_STATUSES)
            ->where(function($query) {
                $query->where('customer_eligibility', 'Guaranteed Issue')
                      ->orWhere('customer_eligibility', 'Graded GTL');
            })
            ->count();
        
        $levelPercent = $data->approved_count > 0 ? round(($levelCount / $data->approved_count) * 100) : 0;
        $giPercent = $data->approved_count > 0 ? round(($giCount / $data->approved_count) * 100) : 0;
        
        // Ensure percentages don't exceed 100%
        $levelPercent = min($levelPercent, 100);
        $giPercent = min($giPercent, 100);
        
        // Handle edge case where percentages exceed 100% combined
        if ($levelPercent + $giPercent > 100) {
            $total = $levelPercent + $giPercent;
            $levelPercent = round(($levelPercent / $total) * 100);
            $giPercent = round(($giPercent / $total) * 100);
        }
        
        return [
            'total_submissions' => $data->total_submissions ?? 0,
            'approved_count' => $data->approved_count ?? 0,
            'pending_count' => ($data->total_submissions ?? 0) - ($data->approved_count ?? 0),
            'conversion_rate' => $data->conversion_rate ?? 0,
            'avg_premium' => $data->avg_premium ?? 0,
            'yearly_premium_estimate' => round(($data->total_premium_volume ?? 0) * 12, 2),
            'level_percent' => $levelPercent,
            'gi_percent' => $giPercent,
        ];
        
    } catch (\Exception $e) {
        Log::error('My client data error: ' . $e->getMessage());
        return [
            'total_submissions' => 0,
            'approved_count' => 0,
            'pending_count' => 0,
            'conversion_rate' => 0,
            'avg_premium' => 0,
            'yearly_premium_estimate' => 0,
            'level_percent' => 0,
            'gi_percent' => 0,
        ];
    }
}

// Get specific client's carrier counts
private function getMyCarrierCounts($clientId, $startDate, $endDate)
{
    try {
        $carrierCounts = [];
        
        foreach (self::ALL_CARRIERS as $carrier) {
            $count = ClosedCall::where('clients_id', $clientId)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->whereIn('status', self::APPROVED_STATUSES)
                ->where('carrier', $carrier)
                ->count();
            
            $carrierCounts[$carrier] = $count;
        }
        
        return $carrierCounts;
        
    } catch (\Exception $e) {
        Log::error('My carrier counts error: ' . $e->getMessage());
        return [];
    }
}

// Get specific client's submissions with all details
private function getMySubmissions($clientId, $startDate, $endDate)
{
    try {
        $submissions = ClosedCall::where('clients_id', $clientId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Convert created_at to New York timezone for display
        $submissions->each(function($submission) {
            $submission->created_at = $submission->created_at->setTimezone('America/New_York');
        });
        
        return $submissions;
        
    } catch (\Exception $e) {
        Log::error('My submissions error: ' . $e->getMessage());
        return collect();
    }
}

// Export client's own data
public function exportMyReport(Request $request)
{
    try {
        $filter = $request->get('filter', 'monthly');
        $currentDate = Carbon::now('America/New_York'); // New York timezone
        $clientId = auth()->id();
        
        // Check if user is actually a client
        if (auth()->user()->type !== 'client') {
            abort(403, 'This export is only accessible to clients.');
        }
        
        switch ($filter) {
            case 'daily':
                // Adjust for night shift
                $adjustedDate = $currentDate->copy()->subHours(12);
                $startDate = $adjustedDate->copy()->startOfDay()->addHours(12)->utc();
                $endDate = $adjustedDate->copy()->addDay()->startOfDay()->addHours(12)->subMinute()->utc();
                $period = 'Daily';
                break;
            case 'weekly':
                $startDate = $currentDate->copy()->startOfWeek()->utc();
                $endDate = $currentDate->copy()->endOfWeek()->utc();
                $period = 'Weekly';
                break;
            default:
                $startDate = $currentDate->copy()->startOfMonth()->utc();
                $endDate = $currentDate->copy()->endOfMonth()->utc();
                $period = 'Monthly';
                break;
        }
        
        $myData = $this->getMyClientData($clientId, $startDate, $endDate);
        
        // Get detailed submissions
        $submissions = ClosedCall::where('clients_id', $clientId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Convert to New York timezone for export
        $submissions->each(function($submission) {
            $submission->created_at = $submission->created_at->setTimezone('America/New_York');
        });
        
        $filename = "my_report_{$filter}_" . $currentDate->format('Y_m_d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        
        $callback = function() use ($myData, $submissions, $period, $startDate, $endDate) {
            $file = fopen('php://output', 'w');
            
            // Convert dates to New York timezone for display
            $displayStartDate = Carbon::parse($startDate)->setTimezone('America/New_York');
            $displayEndDate = Carbon::parse($endDate)->setTimezone('America/New_York');
            
            // Add header info
            fputcsv($file, ["My Client Report - $period"]);
            fputcsv($file, ["Client: " . auth()->user()->name]);
            fputcsv($file, ["Period: " . $displayStartDate->format('Y-m-d') . " to " . $displayEndDate->format('Y-m-d')]);
            fputcsv($file, ["Generated: " . Carbon::now('America/New_York')->format('Y-m-d H:i:s')]);
            fputcsv($file, ["Timezone: America/New_York (Eastern Time)"]);
            fputcsv($file, []);
            
            // Add summary
            fputcsv($file, ["SUMMARY"]);
            fputcsv($file, ["Total Submissions", $myData['total_submissions']]);
            fputcsv($file, ["Approved", $myData['approved_count']]);
            fputcsv($file, ["Pending", $myData['pending_count']]);
            fputcsv($file, ["Conversion Rate (%)", $myData['conversion_rate']]);
            fputcsv($file, ["Average Premium ($)", $myData['avg_premium']]);
            fputcsv($file, ["Yearly Estimate ($)", $myData['yearly_premium_estimate']]);
            fputcsv($file, ["Level (%)", $myData['level_percent']]);
            fputcsv($file, ["GI (%)", $myData['gi_percent']]);
            fputcsv($file, []);
            
            // Add detailed submissions
            fputcsv($file, ["DETAILED SUBMISSIONS"]);
            fputcsv($file, [
                'Date',
                'Time',
                'Customer Name',
                'Status',
                'Closer',
                'Carrier',
                'Premium ($)',
                'Eligibility',
                'Client Comment',
                'Other Comment'
            ]);
            
            foreach ($submissions as $submission) {
                fputcsv($file, [
                    $submission->created_at->format('Y-m-d'),
                    $submission->created_at->format('h:i A'),
                    $submission->customer_full_name ?? 'N/A',
                    $submission->status,
                    $submission->closername ?? 'N/A',
                    $submission->carrier ?? 'N/A',
                    $submission->monthly_premium ?? '0',
                    $submission->customer_eligibility ?? 'N/A',
                    $submission->clients_comment ?? '',
                    $submission->comments ?? ''
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
        
    } catch (\Exception $e) {
        Log::error('My report export error: ' . $e->getMessage());
        return response()->json(['error' => 'Export failed: ' . $e->getMessage()], 500);
    }
}
public function teamsWise(Request $request)
{
    $range = $request->get('range', 'monthly'); // daily, weekly, monthly, custom
    $month = $request->get('month', now('America/New_York')->toDateString());
    $startDate = $request->get('start_date', now('America/New_York')->toDateString());
    $endDate   = $request->get('end_date', now('America/New_York')->toDateString());

    switch ($range) {
        case 'daily':
            $from = now('America/New_York')->toDateString();
            $to   = now('America/New_York')->toDateString();
            break;
        case 'weekly':
            $from = now('America/New_York')->startOfWeek()->toDateString();
            $to   = now('America/New_York')->toDateString();
            break;
        case 'custom':
            $from = $startDate;
            $to   = $endDate;
            break;
        default:
            $from = \Carbon\Carbon::parse($month)->startOfMonth()->toDateString();
            $to   = min(\Carbon\Carbon::parse($month)->endOfMonth(), now('America/New_York'))->toDateString();
            break;
    }

    $salesService = app(SalesService::class);
    $board = $salesService->teamWiseClosersBoard($from, $to);

    $leaderboard = [];
    try {
        $leaderboard = app(\App\Services\DialerApiService::class)->leaderboard(['from' => $from, 'to' => $to]);
    } catch (\Throwable $e) {
        report($e);
    }

    $teams = $salesService->mergeDialerStatsIntoTeams($board['teams'], $leaderboard);

    return view('sales-reports.team-wise', [
        'month'   => $month,
        'range'   => $range,
        'start_date' => $startDate,
        'end_date'   => $endDate,
        'teams'   => $teams,
        'clients' => $board['clients'],
        'canEdit' => $this->canEdit(),
        'teamBoxes' => app(SalesService::class)->teamBoxes(),
        'allTeams' => \App\Models\SalesTeam::orderBy('name')->get(),
    ]);
}
private function canEdit(): bool
{
    return auth()->check() && in_array(auth()->user()->email, [
        'fazail@jsonscommunications.com',
        'm.muzammil@jsonscommunication.com',
            'ubaid.khan@jsonscommunication.com',
            'hussamjanjua@jsons.com',
            'furqankashif@jsons.com',
            'sheikh.noman@jsonscommunication.com',
            'taimoorjanjua@mgmt.jsonscommunications.com',
            'aslambaig@jsons.com',
    ]);
}
public function monthlyReports(Request $request)
{
    $month = $request->get('month', now()->toDateString());
    $salesService = app(SalesService::class);

    return view('sales-reports.index', [
        'month'          => $month,
        'closersReport'  => $salesService->monthlyClosersReport($month),
        'clientsReport'  => $salesService->monthlyClientsReport($month),
        'teamsReport'    => $salesService->monthlyTeamsReport($month),
        'carriersReport' => $salesService->monthlyCarriersReport($month),
        'clients'        => \App\Models\SalesClient::orderBy('name')->get(),
        'carriers'       => \App\Models\SalesCarrier::orderBy('name')->get(),
    ]);
}

public function clientWise(Request $request)
{
    [$from, $to, $range, $month] = $this->resolveDateRange($request);

    $salesService = app(SalesService::class);
    $matrix = $salesService->closerClientMatrix($from, $to);

    return view('sales-reports.client-wise', [
        'month'   => $month,
        'range'   => $range,
        'summary' => $salesService->monthlyClientsReport($from, $to),
        'rows'    => $matrix['rows'],
        'clients' => $matrix['clients'],
        'canEdit' => $this->canEdit(),
    ]);
}

public function carrierWise(Request $request)
{
    [$from, $to, $range, $month] = $this->resolveDateRange($request);

    $salesService = app(SalesService::class);

    $leaderboard = [];
    try {
        $leaderboard = app(\App\Services\DialerApiService::class)->leaderboard(['from' => $from, 'to' => $to]);
    } catch (\Throwable $e) {
        report($e);
    }

    $matrix = $salesService->closerCarrierMatrix($from, $to, $leaderboard);

    return view('sales-reports.carrier-wise', [
        'month'    => $month,
        'range'    => $range,
        'summary'  => $salesService->monthlyCarriersReport($from, $to),
        'rows'     => $matrix['rows'],
        'carriers' => $matrix['carriers'],
        'canEdit'  => $this->canEdit(),
    ]);
}

/**
 * Shared daily/weekly/monthly date-range resolver — used by client-wise,
 * carrier-wise, and team-wise so all three filter the same way.
 */
protected function resolveDateRange(Request $request): array
{
    $range = $request->get('range', 'monthly');
    $month = $request->get('month', now('America/New_York')->toDateString());

    switch ($range) {
        case 'daily':
            $from = now('America/New_York')->toDateString();
            $to   = now('America/New_York')->toDateString();
            break;
        case 'weekly':
            $from = now('America/New_York')->startOfWeek()->toDateString();
            $to   = now('America/New_York')->toDateString();
            break;
        default:
            $from = \Carbon\Carbon::parse($month)->startOfMonth()->toDateString();
            $to   = min(\Carbon\Carbon::parse($month)->endOfMonth(), now('America/New_York'))->toDateString();
            break;
    }

    return [$from, $to, $range, $month];
}
} // <-- class ki closing brace

