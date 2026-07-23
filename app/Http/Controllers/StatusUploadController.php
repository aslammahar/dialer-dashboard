<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClosedCall;
use App\Models\StatusChangeLog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;

class StatusUploadController extends Controller
{
    private const APPROVED_STATUSES = [
        'Funded',
        'charged_backed',
        'Approved',
        'Potential Lapsed',
    ];

    // ─── Show the main upload page ─────────────────────────────────────────────
   // ─── Show the main upload page ─────────────────────────────────────────────
public function index(Request $request)
{
    if (!auth()->user()->can('closing status update')) {
        abort(403, 'You do not have permission to view this page.');
    }

    $perPage  = $request->input('per_page', 20);
    $search   = $request->input('search');
    $source   = $request->input('source_filter');
    $batch    = $request->input('batch_filter');

    $query = StatusChangeLog::with(['closedCall', 'changedBy'])
        ->orderBy('changed_at', 'desc');

    if ($search) {
        $query->where(function ($q) use ($search) {
            $q->where('policy_id',      'LIKE', "%{$search}%")
              ->orWhere('customer_name', 'LIKE', "%{$search}%")
              ->orWhere('old_status',    'LIKE', "%{$search}%")
              ->orWhere('new_status',    'LIKE', "%{$search}%");
        });
    }

    if ($source && $source !== 'all') {
        $query->where('source_file', $source);
    }

    if ($batch) {
        $query->where('upload_batch', $batch);
    }

    $logs = $query->paginate($perPage);

    // ─── Preload full status chains (no N+1) ──────────────────────────────
    $closedCallIds = $logs->pluck('closed_call_id')->unique();
    $statusChains  = StatusChangeLog::whereIn('closed_call_id', $closedCallIds)
        ->orderBy('changed_at', 'asc')
        ->get()
        ->groupBy('closed_call_id');

    $batches = StatusChangeLog::select('upload_batch', 'source_file', 'changed_at')
        ->orderBy('changed_at', 'desc')
        ->get()
        ->groupBy('upload_batch')
        ->map(fn($items) => [
            'batch'      => $items->first()->upload_batch,
            'source'     => $items->first()->source_file,
            'changed_at' => $items->first()->changed_at,
            'count'      => $items->count(),
        ])
        ->values()
        ->take(30);

    // ─── Missing Policy ID section ─────────────────────────────────────────
    $missingPerPage   = $request->input('missing_per_page', 20);
    $missingStatus    = $request->input('missing_status');
    $missingStartDate = $request->input('missing_start_date');
    $missingEndDate   = $request->input('missing_end_date');

    $missingQuery = ClosedCall::whereIn('status', self::APPROVED_STATUSES)
        ->where(function ($q) {
            $q->whereNull('policy_id')
              ->orWhere('policy_id', '');
        })
        ->orderBy('created_at', 'desc');

    if ($missingStatus) {
        $missingQuery->where('status', $missingStatus);
    }
    if ($missingStartDate) {
        $missingQuery->whereDate('created_at', '>=', $missingStartDate);
    }
    if ($missingEndDate) {
        $missingQuery->whereDate('created_at', '<=', $missingEndDate);
    }

    $missingRecords    = $missingQuery->paginate($missingPerPage, ['*'], 'missing_page');
    $missingTotalCount = ClosedCall::whereIn('status', self::APPROVED_STATUSES)
        ->where(function ($q) {
            $q->whereNull('policy_id')->orWhere('policy_id', '');
        })->count();

    return view('status-upload.index', compact(
        'logs', 'batches', 'perPage', 'search', 'source', 'batch',
        'statusChains',
        'missingRecords', 'missingStatus', 'missingStartDate', 'missingEndDate',
        'missingPerPage', 'missingTotalCount'
    ));
}

    // ─── Handle Lapse Report Upload ────────────────────────────────────────────
    public function uploadLapseReport(Request $request)
    {
        if (!auth()->user()->can('closing status update')) {
            abort(403, 'You do not have permission to upload lapse reports.');
        }

        $request->validate([
            'lapse_file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        $file          = $request->file('lapse_file');
        $today         = Carbon::today();
        $batch         = Str::uuid()->toString();
        $userId        = Auth::id();
        $updated       = 0;
        $skipped       = 0;
        $notFound      = 0;
        $errors        = [];
        $unmatchedRows = [];

        try {
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet       = $spreadsheet->getActiveSheet();
            $rows        = $sheet->toArray(null, true, true, true);

            $headers   = array_map('trim', array_shift($rows));
            $headerMap = array_flip($headers);

            $policyCol = $this->findCol($headerMap, ['Policy #', 'Policy#', 'PolicyNumber', 'Policy Number']);
            $paidToCol = $this->findCol($headerMap, ['Paid To Date', 'PaidToDate']);

            if (!$policyCol || !$paidToCol) {
                return back()->with('error', 'Lapse Report must have "Policy #" and "Paid To Date" columns.');
            }

            foreach ($rows as $row) {
                $policyId  = trim($row[$policyCol] ?? '');
                $paidToRaw = trim($row[$paidToCol] ?? '');

                if (empty($policyId) || empty($paidToRaw)) {
                    $skipped++;
                    continue;
                }

                try {
                    $paidToDate = Carbon::parse($paidToRaw);
                } catch (\Exception $e) {
                    $errors[] = "Could not parse date '{$paidToRaw}' for policy {$policyId}";
                    $skipped++;
                    continue;
                }

                $call = ClosedCall::where('policy_id', $policyId)->first();

                if (!$call) {
                    $notFound++;
                    // Store ALL unmatched rows in session for export (no 100 limit)
                    $unmatchedRows[] = [
                        'policy_id'    => $policyId,
                        'paid_to_date' => $paidToRaw,
                        'owner'        => trim($row['D'] ?? ''),
                    ];
                    continue;
                }

                $newStatus = $paidToDate->greaterThanOrEqualTo($today) ? 'Funded' : 'Potential Lapsed';
                $oldStatus = $call->status;

                if ($oldStatus === $newStatus) {
                    $skipped++;
                    continue;
                }

                $call->status = $newStatus;
                $call->save();

                StatusChangeLog::create([
                    'closed_call_id' => $call->id,
                    'policy_id'      => $policyId,
                    'customer_name'  => $call->customer_full_name,
                    'old_status'     => $oldStatus,
                    'new_status'     => $newStatus,
                    'source_file'    => 'lapse_report',
                    'upload_batch'   => $batch,
                    'changed_by'     => $userId,
                    'changed_at'     => now(),
                    'paid_to_date'   => $paidToDate->toDateString(),
                    'description'    => null,
                ]);

                $updated++;
            }

            $summary = "Lapse Report processed: {$updated} updated, {$skipped} skipped (no change / missing data), {$notFound} policy IDs not found.";
            if (!empty($errors)) {
                $summary .= ' Errors: ' . implode('; ', array_slice($errors, 0, 5));
            }

            // Store ALL unmatched rows in session — available for export until next request
            session(['unmatched_lapse' => $unmatchedRows]);

            return back()
                ->with('success', $summary)
                ->with('batch', $batch)
                ->with('unmatched_lapse_count', count($unmatchedRows));

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to process file: ' . $e->getMessage());
        }
    }

    // ─── Handle Monthly Advance Summary Upload ─────────────────────────────────
    public function uploadMonthlyAdvance(Request $request)
    {
        if (!auth()->user()->can('closing status update')) {
            abort(403, 'You do not have permission to upload monthly advance reports.');
        }

        $request->validate([
            'advance_file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        $file          = $request->file('advance_file');
        $batch         = Str::uuid()->toString();
        $userId        = Auth::id();
        $updated       = 0;
        $skipped       = 0;
        $notFound      = 0;
        $errors        = [];
        $unmatchedRows = [];

        $descriptionMap = [
            // Most specific phrases first to prevent partial matches
            'as earned comm - reversal'  => 'Potential Lapsed',
            'clawback as-earned'         => 'charged_backed',
            'chargebacks'                => 'charged_backed',
            'chargeback'                 => 'charged_backed',
            'charge back'                => 'charged_backed',
            'claw back'                  => 'charged_backed',
            'clawback'                   => 'charged_backed',
            'as earned comm'             => 'Funded',
            'advances'                   => 'Funded',
            'advance'                    => 'Funded',
            'as earned'                  => 'Funded',
            'balance correction advance' => 'Funded',
        ];

        try {
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet       = $spreadsheet->getActiveSheet();
            $rows        = $sheet->toArray(null, true, true, true);

            $headers   = array_map('trim', array_shift($rows));
            $headerMap = array_flip($headers);

            $policyCol = $this->findCol($headerMap, ['Policy Number', 'Policy#', 'Policy #', 'PolicyNumber']);
            $descCol   = $this->findCol($headerMap, ['Description']);

            if (!$policyCol || !$descCol) {
                return back()->with('error', 'Monthly Advance file must have "Policy Number" and "Description" columns.');
            }

            foreach ($rows as $row) {
                $policyId = trim($row[$policyCol] ?? '');
                $desc     = strtolower(trim($row[$descCol] ?? ''));

                if (empty($policyId) || empty($desc)) {
                    $skipped++;
                    continue;
                }

                $newStatus = null;
                foreach ($descriptionMap as $keyword => $status) {
                    if (str_contains($desc, $keyword)) {
                        $newStatus = $status;
                        break;
                    }
                }

                if (!$newStatus) {
                    $skipped++;
                    continue;
                }

                $call = ClosedCall::where('policy_id', $policyId)->first();

                if (!$call) {
                    $notFound++;
                    // Store ALL unmatched rows in session for export (no limit)
                    $unmatchedRows[] = [
                        'policy_id'   => $policyId,
                        'description' => $row[$descCol],
                        'insured'     => trim($row['F'] ?? ''),
                    ];
                    continue;
                }

                $oldStatus = $call->status;

                if ($oldStatus === $newStatus) {
                    $skipped++;
                    continue;
                }

                $call->status = $newStatus;
                $call->save();

                StatusChangeLog::create([
                    'closed_call_id' => $call->id,
                    'policy_id'      => $policyId,
                    'customer_name'  => $call->customer_full_name,
                    'old_status'     => $oldStatus,
                    'new_status'     => $newStatus,
                    'source_file'    => 'monthly_advance',
                    'upload_batch'   => $batch,
                    'changed_by'     => $userId,
                    'changed_at'     => now(),
                    'paid_to_date'   => null,
                    'description'    => $row[$descCol],
                ]);

                $updated++;
            }

            $summary = "Monthly Advance processed: {$updated} updated, {$skipped} skipped (no matching description or no change), {$notFound} policy IDs not found.";

            // Store ALL unmatched rows in session — available for export until next request
            session(['unmatched_advance' => $unmatchedRows]);

            return back()
                ->with('success', $summary)
                ->with('batch', $batch)
                ->with('unmatched_advance_count', count($unmatchedRows));

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to process file: ' . $e->getMessage());
        }
    }

    // ─── Export unmatched lapse records from session as CSV ───────────────────
    public function exportUnmatchedLapse(Request $request)
    {
        if (!auth()->user()->can('closing status update')) {
            abort(403);
        }

        $rows     = session('unmatched_lapse', []);
        $filename = 'unmatched_lapse_' . now()->format('Y_m_d_H_i_s') . '.csv';

        return response()->stream(function () use ($rows) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Policy ID', 'Paid To Date', 'Owner']);
            foreach ($rows as $row) {
                fputcsv($file, [
                    $row['policy_id']    ?? '',
                    $row['paid_to_date'] ?? '',
                    $row['owner']        ?? '',
                ]);
            }
            fclose($file);
        }, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    // ─── Export unmatched advance records from session as CSV ─────────────────
    public function exportUnmatchedAdvance(Request $request)
    {
        if (!auth()->user()->can('closing status update')) {
            abort(403);
        }

        $rows     = session('unmatched_advance', []);
        $filename = 'unmatched_advance_' . now()->format('Y_m_d_H_i_s') . '.csv';

        return response()->stream(function () use ($rows) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Policy ID', 'Description', 'Insured']);
            foreach ($rows as $row) {
                fputcsv($file, [
                    $row['policy_id']   ?? '',
                    $row['description'] ?? '',
                    $row['insured']     ?? '',
                ]);
            }
            fclose($file);
        }, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    // ─── Export missing policy ID records as CSV ───────────────────────────────
    public function exportMissingPolicyIds(Request $request)
    {
        if (!auth()->user()->can('closing status update')) {
            abort(403);
        }

        $missingStatus    = $request->input('missing_status');
        $missingStartDate = $request->input('missing_start_date');
        $missingEndDate   = $request->input('missing_end_date');

        $query = ClosedCall::whereIn('status', self::APPROVED_STATUSES)
            ->where(function ($q) {
                $q->whereNull('policy_id')->orWhere('policy_id', '');
            })
            ->orderBy('created_at', 'desc');

        if ($missingStatus) {
            $query->where('status', $missingStatus);
        }
        if ($missingStartDate) {
            $query->whereDate('created_at', '>=', $missingStartDate);
        }
        if ($missingEndDate) {
            $query->whereDate('created_at', '<=', $missingEndDate);
        }

        $records  = $query->get();
        $filename = 'missing_policy_ids_' . now()->format('Y_m_d_H_i_s') . '.csv';

        return response()->stream(function () use ($records) {
            $file = fopen('php://output', 'w');

            fputcsv($file, ['Missing Policy ID Records Export']);
            fputcsv($file, ['Generated: ' . now()->format('Y-m-d H:i:s')]);
            fputcsv($file, ['Total Records: ' . $records->count()]);
            fputcsv($file, []);
            fputcsv($file, [
                'Record ID', 'Date', 'Customer Name', 'Phone', 'State',
                'Status', 'Carrier', 'Coverage Plan', 'Monthly Premium',
                'Closer Name', 'Center Name', 'Team Name', 'Policy ID (empty)',
            ]);

            foreach ($records as $r) {
                fputcsv($file, [
                    $r->id,
                    $r->created_at->format('Y-m-d'),
                    $r->customer_full_name ?? 'N/A',
                    $r->phone_number       ?? 'N/A',
                    $r->state              ?? 'N/A',
                    $r->status             ?? 'N/A',
                    $r->carrier            ?? 'N/A',
                    $r->coverage_plan      ?? 'N/A',
                    $r->monthly_premium    ?? 'N/A',
                    $r->closername         ?? 'N/A',
                    $r->center_name        ?? 'N/A',
                    $r->teamname           ?? 'N/A',
                    '',
                ]);
            }

            fclose($file);
        }, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    // ─── Helper: find column letter by header name candidates ─────────────────
    private function findCol(array $headerMap, array $candidates): ?string
    {
        foreach ($candidates as $name) {
            if (isset($headerMap[$name])) {
                return $headerMap[$name];
            }
        }
        foreach ($headerMap as $header => $col) {
            foreach ($candidates as $name) {
                if (strtolower(trim($header)) === strtolower($name)) {
                    return $col;
                }
            }
        }
        return null;
    }

    // ─── Client Report page ────────────────────────────────────────────────────
    public function clientReport(Request $request)
    {
        if (!auth()->user()->can('closing status update')) {
            abort(403, 'You do not have permission to view this page.');
        }

        $clients = User::where('type', 'client')->where('is_parent', 0)->orderBy('name')->get(['id', 'name']);
        $clientId  = $request->input('client_id');
        $startDate = $request->input('start_date');
        $endDate   = $request->input('end_date');
        $status    = $request->input('status');
        $perPage   = $request->input('per_page', 20);

        $records = null;
        $total   = 0;

        if ($clientId || $startDate || $endDate) {
            $query = ClosedCall::withoutGlobalScopes()
                ->with('client')
                ->orderBy('created_at', 'desc');

            if ($clientId) {
                $query->where('clients_id', $clientId);
            }
            if ($startDate) {
                $query->whereDate('created_at', '>=', $startDate);
            }
            if ($endDate) {
                $query->whereDate('created_at', '<=', $endDate);
            }
            if ($status) {
                $query->where('status', $status);
            }

            $records = $query->paginate($perPage);
            $total   = $query->toBase()->getCountForPagination();
        }

        $statuses = [
            'Funded', 'charged_backed', 'Approved', 'Potential Lapsed',
            'pending','Rejected',
        ];

        return view('status-upload.client-report', compact(
            'clients', 'clientId', 'startDate', 'endDate', 'status',
            'perPage', 'records', 'total', 'statuses'
        ));
    }

    // ─── Export Client Report as CSV ───────────────────────────────────────────
    public function exportClientReport(Request $request)
    {
        if (!auth()->user()->can('closing status update')) {
            abort(403);
        }

        $clientId  = $request->input('client_id');
        $startDate = $request->input('start_date');
        $endDate   = $request->input('end_date');
        $status    = $request->input('status');

        $query = ClosedCall::withoutGlobalScopes()
            ->with('client')
            ->orderBy('created_at', 'desc');

        if ($clientId) {
            $query->where('clients_id', $clientId);
        }
        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }
        if ($status) {
            $query->where('status', $status);
        }

        $records  = $query->get();
        $filename = 'client_report_' . now()->format('Y_m_d_H_i_s') . '.csv';

        return response()->stream(function () use ($records) {
            $file = fopen('php://output', 'w');

            fputcsv($file, ['Client Report Export']);
            fputcsv($file, ['Generated: ' . now()->format('Y-m-d H:i:s')]);
            fputcsv($file, ['Total Records: ' . $records->count()]);
            fputcsv($file, []);
            fputcsv($file, [
                'Record ID', 'Date', 'Client Name', 'Customer Name', 'Phone',
                'State', 'Status', 'Policy ID', 'Carrier', 'Coverage Plan',
                'Monthly Premium', 'Closer Name', 'Center Name', 'Team Name',
            ]);

            foreach ($records as $r) {
                fputcsv($file, [
                    $r->id,
                    $r->created_at->format('Y-m-d'),
                    $r->client?->name ?? 'N/A',
                    $r->customer_full_name ?? 'N/A',
                    $r->phone_number       ?? 'N/A',
                    $r->state              ?? 'N/A',
                    $r->status             ?? 'N/A',
                    $r->policy_id          ?? '',
                    $r->carrier            ?? 'N/A',
                    $r->coverage_plan      ?? 'N/A',
                    $r->monthly_premium    ?? 'N/A',
                    $r->closername         ?? 'N/A',
                    $r->center_name        ?? 'N/A',
                    $r->teamname           ?? 'N/A',
                ]);
            }

            fclose($file);
        }, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    // ─── Delete a single log entry ─────────────────────────────────────────────
    public function deleteLog($id)
    {
        if (!auth()->user()->can('closing status update')) {
            abort(403, 'You do not have permission to delete log entries.');
        }

        $log = StatusChangeLog::findOrFail($id);
        $log->delete();

        return back()->with('success', 'Log entry deleted.');
    }
}