<?php

namespace App\Http\Controllers;

use App\Models\ClosedCall;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ClosedCallsController extends Controller
{
    /**
     * Allowed emails for accessing this page
     */
    private const ALLOWED_EMAILS = [
        'chaudharynoman@sellerz.com',
        'jamilahmad7666@gmail.com',
    ];

    /**
     * Center name to filter closed calls
     */
    private const CENTER_NAME = 'sellerz';

    /**
     * Cache duration in minutes
     */
    private const CACHE_DURATION = 15;

    /**
     * Check if the current user is authorized to access this page
     */
    private function isAuthorized(): bool
    {
        if (!Auth::check()) {
            return false;
        }

        $userEmail = Auth::user()->email;

        if (!in_array($userEmail, self::ALLOWED_EMAILS, true)) {
            Log::warning('Unauthorized access attempt to Closed Calls page', [
                'attempted_email' => $userEmail,
                'allowed_emails' => self::ALLOWED_EMAILS,
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);
            return false;
        }

        return true;
    }

    /**
     * Display a listing of closed calls for sellerz center
     */
    public function index(Request $request)
    {
        // Check authorization
        if (!$this->isAuthorized()) {
            abort(403, 'Unauthorized access. This page is restricted to authorized personnel only.');
        }

        try {
            $perPage = $request->get('per_page', 25);
            $search = $request->get('search', '');
            $status = $request->get('status', '');
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');

            // Validate sort column - expanded list of sortable fields
            $allowedSortColumns = [
                'id', 'customer_full_name', 'phone_number', 'alternate_phone_number', 'cx_email',
                'address', 'city', 'state', 'zip_code', 'gender', 'martial_status', 'age', 'dob',
                'center_name', 'status', 'closer_id', 'closername', 'junior_closer_name',
                'lead_id', 'policy_id', 'hippa_id', 'recording_id', 'created_at', 'updated_at'
            ];
            if (!in_array($sortBy, $allowedSortColumns)) {
                $sortBy = 'created_at';
            }

            // Validate sort order
            $sortOrder = strtolower($sortOrder) === 'asc' ? 'asc' : 'desc';

            $centerId = Auth::user()->center_id ?? 'no_center';

            // Build cache key (include center_id to avoid cross-center cache leakage)
            $cacheKey = "sellerz_closed_calls_center_{$centerId}_page_{$perPage}_{$search}_{$status}_{$sortBy}_{$sortOrder}_" . $request->get('page', 1);

            // Get paginated closed calls with caching
            $closedCalls = Cache::remember($cacheKey, now()->addMinutes(self::CACHE_DURATION), function () use ($perPage, $search, $status, $sortBy, $sortOrder) {
                $query = ClosedCall::where('center_name', self::CENTER_NAME);

                // Apply search filter - expanded to include all relevant fields
                if (!empty($search)) {
                    $query->where(function ($q) use ($search) {
                        $q->where('customer_full_name', 'like', "%{$search}%")
                            ->orWhere('phone_number', 'like', "%{$search}%")
                            ->orWhere('alternate_phone_number', 'like', "%{$search}%")
                            ->orWhere('cx_email', 'like', "%{$search}%")
                            ->orWhere('lead_id', 'like', "%{$search}%")
                            ->orWhere('policy_id', 'like', "%{$search}%")
                            ->orWhere('hippa_id', 'like', "%{$search}%")
                            ->orWhere('recording_id', 'like', "%{$search}%")
                            ->orWhere('closername', 'like', "%{$search}%")
                            ->orWhere('junior_closer_name', 'like', "%{$search}%")
                            ->orWhere('agentname', 'like', "%{$search}%")
                            ->orWhere('dialeragentname', 'like', "%{$search}%")
                            ->orWhere('dialername', 'like', "%{$search}%")
                            ->orWhere('teamname', 'like', "%{$search}%");
                    });
                }

                // Apply status filter
                if (!empty($status)) {
                    $query->where('status', $status);
                }

                // Apply sorting
                $query->orderBy($sortBy, $sortOrder);

                return $query->paginate($perPage);
            });

            // Get statistics with caching
            $statsCacheKey = "sellerz_closed_calls_stats_center_{$centerId}";
            $stats = Cache::remember($statsCacheKey, now()->addMinutes(self::CACHE_DURATION), function () {
                $baseQuery = ClosedCall::where('center_name', self::CENTER_NAME);
                
                return [
                    'total' => (clone $baseQuery)->count(),
                    'pending' => (clone $baseQuery)->where('status', 'pending')->count(),
                    'approved' => (clone $baseQuery)->where('status', 'approved')->count(),
                    'rejected' => (clone $baseQuery)->where('status', 'rejected')->count(),
                    'funded' => (clone $baseQuery)->where('status', 'funded')->count(),
                    'charged_backed' => (clone $baseQuery)->where('status', 'charged_backed')->count(),
                ];
            });

            // Log access
            Log::info('Closed Calls page accessed', [
                'user_email' => Auth::user()->email,
                'user_id' => Auth::user()->id,
                'ip' => request()->ip(),
                'page' => $request->get('page', 1),
                'per_page' => $perPage
            ]);

            return view('closed-calls.index', compact('closedCalls', 'stats', 'search', 'status', 'sortBy', 'sortOrder', 'perPage'));

        } catch (\Exception $e) {
            Log::error('Error loading Closed Calls page', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_email' => Auth::user()->email ?? 'N/A'
            ]);

            return redirect()->back()->with('error', 'An error occurred while loading the page. Please try again.');
        }
    }

    /**
     * Clear cache for sellerz closed calls
     */
    public function clearCache()
    {
        if (!$this->isAuthorized()) {
            abort(403, 'Unauthorized access.');
        }

        try {
            // Clear all sellerz closed calls cache
            Cache::flush(); // Or use more specific cache tags if using cache tags

            Log::info('Closed Calls cache cleared', [
                'user_email' => Auth::user()->email,
                'user_id' => Auth::user()->id
            ]);

            return redirect()->route('center_reports.index')->with('success', 'Cache cleared successfully!');
        } catch (\Exception $e) {
            Log::error('Error clearing cache', [
                'error' => $e->getMessage(),
                'user_email' => Auth::user()->email ?? 'N/A'
            ]);

            return redirect()->back()->with('error', 'Failed to clear cache.');
        }
    }
}
