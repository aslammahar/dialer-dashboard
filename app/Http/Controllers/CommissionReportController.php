<?php

namespace App\Http\Controllers;

use App\Models\CommissionStatement;
use App\Models\AgentConfig;
use App\Models\ClosedCall;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Carbon\Carbon;

class CommissionReportController extends Controller
{
    // ── CACHED CONFIGS FOR PERFORMANCE ──────────────────────────────────────
    private static $configCache = [];

    // ─────────────────────────────────────────────────────────────────────────

    public function index()
    {
        if (!auth()->user()->can('view commissions')) {
            abort(403);
        }

        $months = CommissionStatement::getAvailableMonths();
        $agents = AgentConfig::where('is_active', true)->get();

        return view('commissions.index', compact('months', 'agents'));
    }

    // ─────────────────────────────────────────────────────────────────────────

    public function uploadStatement(Request $request)
    {
        if (!auth()->user()->can('upload commission statement')) {
            abort(403);
        }

        $request->validate([
            'file'  => 'required|file|mimes:xlsx,xls',
            'month' => 'required|integer|min:1|max:12',
            'year'  => 'required|integer|min:2020',
        ]);

        try {
            DB::beginTransaction();

            $file      = $request->file('file');
            $month     = $request->month;
            $year      = $request->year;
            $monthName = Carbon::create($year, $month, 1)->format('F Y');

            $spreadsheet = IOFactory::load($file->getRealPath());
            $worksheet   = $spreadsheet->getActiveSheet();
            $rows        = $worksheet->toArray();

            $dataRows   = array_slice($rows, 1);
            $insertData = [];
            $skipped    = 0;
            $now        = now();

            foreach ($dataRows as $row) {
                if (empty($row[0]) && empty($row[4])) {
                    $skipped++;
                    continue;
                }

                $insertData[] = [
                    'agent_name'        => $row[0]  ?? null,
                    'agent_no'          => $row[1]  ?? null,
                    'level'             => $row[2]  ?? null,
                    'contract_code'     => $row[3]  ?? null,
                    'policy_no'         => $row[4]  ?? null,
                    'insured_name'      => $row[5]  ?? null,
                    'plan_name'         => $row[6]  ?? null,
                    'issue_date'        => $this->parseDate($row[7]  ?? null),
                    'process_date'      => $this->parseDate($row[8]  ?? null),
                    'due_date'          => $this->parseDate($row[9]  ?? null),
                    'check_date'        => $this->parseDate($row[10] ?? null),
                    'check_no'          => $row[11] ?? null,
                    'annual_premium'    => $row[12] ?? null,
                    'monthly_premium'   => $row[13] ?? null,
                    'commission_rate'   => $row[14] ?? null,
                    'description'       => $row[15] ?? null,
                    'debit'             => $row[16] ?? null,
                    'commission_credit' => $row[17] ?? null,
                    'balance'           => $row[18] ?? null,
                    'parent_id'         => $row[22] ?? null,
                    'month'             => $monthName,
                    'year'              => $year,
                    'month_no'          => $month,
                    'file_name'         => $file->getClientOriginalName(),
                    'created_at'        => $now,
                    'updated_at'        => $now,
                ];
            }

            if (!empty($insertData)) {
                CommissionStatement::insert($insertData);
            }

            $imported = count($insertData);

            DB::commit();

            return redirect()->back()->with('success', "Imported {$imported} records for {$monthName}. Skipped {$skipped} empty rows.");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Upload failed: ' . $e->getMessage());
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // SINGLE MONTH REPORT
    // ─────────────────────────────────────────────────────────────────────────

    public function showReport(Request $request)
    {
        if (!auth()->user()->can('view commission report')) {
            abort(403);
        }

        $year  = $request->input('year',  date('Y'));
        $month = $request->input('month', date('m'));
        $type  = $request->input('type',  'all');

        $query = CommissionStatement::with([
            'closedCall:policy_id,customer_full_name,closername'
        ])
        ->select(
            'policy_no', 'agent_name', 'insured_name', 'issue_date',
            'due_date', 'process_date', 'monthly_premium', 'annual_premium',
            'description', 'commission_credit', 'commission_rate', 'month'
        )
        ->byMonth($year, $month);

        $statements = $query->get()->groupBy('policy_no');

        $data = [];

        foreach ($statements as $policyNo => $stmts) {
            $first      = $stmts->first();
            $closedCall = $first->closedCall;

            $totalRevenue = $stmts->sum('commission_credit');
            $lastProcess  = $stmts->whereNotNull('process_date')
                                   ->sortByDesc('process_date')
                                   ->first()?->process_date;

            $data[] = [
                'policy_no'       => $policyNo,
                'schedule_date'   => $first->issue_date,
                'draft_date'      => $first->due_date,
                'process_date'    => $first->process_date,
                'last_updated'    => $lastProcess,
                'insured_name'    => $closedCall->customer_full_name ?? $first->insured_name,
                'closer_name'     => $closedCall->closername ?? 'N/A',
                'client_name'     => $first->agent_name,
                'monthly_premium' => $first->monthly_premium ?? 0,
                'annual_premium'  => $first->annual_premium  ?? 0,
                'description'     => $first->description,
                'total_revenue'   => $totalRevenue,
                'is_closer'       => $closedCall !== null,
                'statements'      => $stmts, // ✅ ADDED for detail view
            ];
        }

        if ($type === 'closers') {
            $data = collect($data)->where('is_closer', true)->values()->all();
        } elseif ($type === 'agents') {
            $data = collect($data)->where('is_closer', false)->values()->all();
        }

        $monthName = Carbon::create($year, $month, 1)->format('F Y');

        return view('commissions.report', compact('data', 'year', 'month', 'monthName', 'type'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // COMPREHENSIVE REPORT - ALL MONTHS
    // ─────────────────────────────────────────────────────────────────────────

    public function showComprehensiveReport(Request $request)
    {
        if (!auth()->user()->can('view comprehensive report')) {
            abort(403);
        }

        $type      = $request->input('type', 'all');
        $fromYear  = $request->input('from_year');
        $fromMonth = $request->input('from_month');
        $toYear    = $request->input('to_year',  date('Y'));
        $toMonth   = $request->input('to_month', date('m'));
        $perPage   = (int) $request->input('per_page', 25);

        $query = CommissionStatement::with([
            'closedCall:policy_id,customer_full_name,closername'
        ])
        ->select(
            'policy_no', 'agent_name', 'insured_name', 'issue_date',
            'process_date', 'due_date', 'monthly_premium', 'annual_premium',
            'description', 'commission_credit', 'commission_rate', 
            'month', 'year', 'month_no'
        );

        if ($fromYear && $fromMonth) {
            $query->where(function ($q) use ($fromYear, $fromMonth, $toYear, $toMonth) {
                $q->where('year', '>', $fromYear)
                  ->orWhere(function ($q2) use ($fromYear, $fromMonth) {
                      $q2->where('year', $fromYear)->where('month_no', '>=', $fromMonth);
                  });
            })->where(function ($q) use ($toYear, $toMonth) {
                $q->where('year', '<', $toYear)
                  ->orWhere(function ($q2) use ($toYear, $toMonth) {
                      $q2->where('year', $toYear)->where('month_no', '<=', $toMonth);
                  });
            });
        }

        $allStatements = $query
            ->orderBy('year')->orderBy('month_no')->orderBy('process_date')
            ->get()
            ->groupBy('policy_no');

        $data = [];

        foreach ($allStatements as $policyNo => $stmts) {
            $first         = $stmts->first();
            $closedCall    = $first->closedCall;
            $totalRevenue  = $stmts->sum('commission_credit');
            $lastProcess   = $stmts->whereNotNull('process_date')->sortByDesc('process_date')->first()?->process_date;
            $pendingAmount = $this->calculatePendingAmountFast($stmts, $first->agent_name);

            $data[] = [
                'policy_no'          => $policyNo,
                'schedule_date'      => $first->issue_date,
                'process_date'       => $first->process_date,
                'draft_date'         => $first->due_date,
                'last_updated'       => $lastProcess,
                'insured_name_excel' => $first->insured_name,
                'customer_full_name' => $closedCall->customer_full_name ?? null,
                'closer_name'        => $closedCall->closername ?? 'N/A',
                'client_name'        => $first->agent_name,
                'monthly_premium'    => $first->monthly_premium ?? 0,
                'annual_premium'     => $first->annual_premium  ?? 0,
                'description'        => $first->description,
                'total_revenue'      => $totalRevenue,
                'pending_amount'     => $pendingAmount,
                'is_closer'          => $closedCall !== null,
                'statements'         => $stmts,
            ];
        }

        if ($type === 'closers') {
            $data = collect($data)->where('is_closer', true)->values()->all();
        } elseif ($type === 'agents') {
            $data = collect($data)->where('is_closer', false)->values()->all();
        }

        $totals = [
            'total_revenue'   => collect($data)->sum('total_revenue'),
            'monthly_premium' => collect($data)->sum('monthly_premium'),
            'pending_amount'  => collect($data)->sum('pending_amount'),
        ];

        $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage();
        $collection  = collect($data);
        $pagedItems  = $collection->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $data = new \Illuminate\Pagination\LengthAwarePaginator(
            $pagedItems,
            $collection->count(),
            $perPage,
            $currentPage,
            ['path' => \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPath()]
        );

        return view('commissions.comprehensives', compact(
            'data', 'totals', 'type',
            'fromYear', 'fromMonth', 'toYear', 'toMonth', 'perPage'
        ));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // ✅ FIXED PENDING CALCULATION
    // ─────────────────────────────────────────────────────────────────────────

    private function calculatePendingAmountFast($statements, $agentName)
    {
        // Check cache first
        if (!isset(self::$configCache[$agentName])) {
            self::$configCache[$agentName] = AgentConfig::where('agent_name', $agentName)
                ->select('advance_months')
                ->first();
        }

        $config = self::$configCache[$agentName];

        if (!$config) {
            return 0;
        }

        // Only calculate if there's an "Advance" in description
        $hasAdvance = $statements->contains(function($stmt) {
            return stripos($stmt->description, 'Advance') !== false;
        });

        if (!$hasAdvance) {
            return 0;
        }

        $monthlyPremium = $statements->first()->monthly_premium ?? 0;
        $advanceMonths  = $config->advance_months;

        // ✅ CORRECTED FORMULA:
        // Total payment period is ALWAYS 12 months
        // If advance is 6 months → pending is 6 months (12 - 6 = 6)
        // If advance is 8 months → pending is 4 months (12 - 8 = 4)
        $pendingMonths = 12 - $advanceMonths;

        return $pendingMonths * $monthlyPremium;
    }

    // ─────────────────────────────────────────────────────────────────────────

    public function showPending(Request $request)
    {
        $agentName = $request->input('agent');

        if ($agentName) {
            $config = AgentConfig::where('agent_name', $agentName)->first();

            if (!$config) {
                return redirect()->back()->with('error', 'Agent not found.');
            }

            $pending = $config->calculatePending();

            return view('commissions.pending', compact('pending', 'config'));
        }

        $agents = AgentConfig::where('is_active', true)->get();
        return view('commissions.pending-select', compact('agents'));
    }

    // ─────────────────────────────────────────────────────────────────────────

    public function storeConfig(Request $request)
    {
        if (!auth()->user()->can('manage commission agent')) {
            abort(403);
        }

        $request->validate([
            'agent_name'     => 'required|string|max:255',
            'advance_months' => 'required|integer|min:1|max:60',
            'notes'          => 'nullable|string',
        ]);

        AgentConfig::updateOrCreate(
            ['agent_name' => $request->agent_name],
            [
                'advance_months' => $request->advance_months,
                'notes'          => $request->notes,
                'is_active'      => true,
            ]
        );

        return redirect()->back()->with('success', 'Agent config saved.');
    }

    // ─────────────────────────────────────────────────────────────────────────

    public function deleteConfig($id)
    {
        if (!auth()->user()->can('manage commission agent')) {
            abort(403);
        }

        AgentConfig::findOrFail($id)->delete();

        return redirect()->back()->with('success', 'Config deleted.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // HELPER: Parse Excel dates
    // ─────────────────────────────────────────────────────────────────────────

    private function parseDate($value)
    {
        if (empty($value)) return null;

        if (is_string($value)) {
            try {
                return Carbon::parse($value)->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }

        if (is_numeric($value)) {
            try {
                $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);
                return $date->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }

        return null;
    }
}