<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SyncAvatarRecordingLinks extends Command
{
    /**
     * recordings:sync-avatar-links {--from=2026-04-01} {--to=2026-05-20}
     *   {--prefix=https://dial4leads.site}
     *   {--chunk=500}
     *   {--dry-run}
     */
    protected $signature = 'recordings:sync-avatar-links '
        . '{--from=2026-04-01 : Start date (inclusive) on avatar_leads.created_at} '
        . '{--to=2026-05-20 : End date (inclusive) on avatar_leads.created_at} '
        . '{--prefix=https://dial4leads.site : Treat any recording_link starting with this as already good} '
        . '{--chunk=500 : Number of avatar_leads processed per batch} '
        . '{--no-verify : Do not check that the local file actually exists in storage} '
        . '{--dry-run : Show what would change without writing}';

    protected $description = 'For avatar_leads in a date range whose recording_link is missing or does not start with the given prefix, build the local storage URL (e.g. https://dial4leads.site/storage/recordings/<filename>.mp3) from the latest downloaded recording for that lead and write it to avatar_leads.recording_link.';

    public function handle(): int
    {
        try {
            $from = Carbon::parse($this->option('from'))->startOfDay();
            $to   = Carbon::parse($this->option('to'))->endOfDay();
        } catch (\Exception $e) {
            $this->error('Invalid --from or --to. Use YYYY-MM-DD.');
            return Command::FAILURE;
        }

        $prefix  = (string) $this->option('prefix');
        $chunk   = max(50, (int) $this->option('chunk'));
        $dryRun  = (bool) $this->option('dry-run');
        $verify  = !$this->option('no-verify');

        $appUrl  = rtrim(config('app.url'), '/');
        $disk    = Storage::disk('public');

        $this->info("Range: {$from->toDateTimeString()} -> {$to->toDateTimeString()}");
        $this->info("Skipping rows whose recording_link already starts with: {$prefix}");
        $this->info('Local URL pattern: ' . $appUrl . '/storage/recordings/<filename>.mp3');
        if (!$verify) {
            $this->warn('File existence check disabled (--no-verify).');
        }
        if ($dryRun) {
            $this->warn('Dry-run mode: no changes will be saved.');
        }

        // Base query: avatar_leads needing a link
        $baseQuery = DB::table('avatar_leads')
            ->whereBetween('created_at', [$from, $to])
            ->whereNotNull('lead_id')
            ->where(function ($q) use ($prefix) {
                $q->whereNull('recording_link')
                  ->orWhere('recording_link', '')
                  ->orWhere('recording_link', 'not like', $prefix . '%');
            });

        $total = (clone $baseQuery)->count();
        $this->info("avatar_leads candidates: {$total}");

        if ($total === 0) {
            return Command::SUCCESS;
        }

        $bar     = $this->output->createProgressBar($total);
        $bar->start();
        $updated     = 0;
        $noMatch     = 0;
        $noFilename  = 0;
        $fileMissing = 0;
        $errors      = 0;

        // Use chunkById to walk safely without re-reading updated rows
        $baseQuery->select(['id', 'lead_id'])
            ->orderBy('id')
            ->chunkById(
                $chunk,
                function ($leads) use (
                    &$updated, &$noMatch, &$noFilename, &$fileMissing, &$errors,
                    $bar, $dryRun, $verify, $appUrl, $disk
                ) {
                    // Collect distinct lead_ids in this chunk
                    $leadIds = $leads->pluck('lead_id')
                        ->filter(fn ($v) => $v !== null && $v !== '')
                        ->map(fn ($v) => (string) $v)
                        ->unique()
                        ->values();

                    if ($leadIds->isEmpty()) {
                        $bar->advance(count($leads));
                        return true;
                    }

                    // For each lead_id, find latest downloaded recording (id + filename)
                    $rows = DB::table('recordings as r')
                        ->select('r.lead_id', 'r.recording_filename', 'r.id')
                        ->joinSub(
                            DB::table('recordings')
                                ->select('lead_id', DB::raw('MAX(id) as max_id'))
                                ->whereIn('lead_id', $leadIds)
                                ->where('status', 'downloaded')
                                ->whereNotNull('recording_filename')
                                ->where('recording_filename', '!=', '')
                                ->groupBy('lead_id'),
                            'latest',
                            function ($join) {
                                $join->on('latest.max_id', '=', 'r.id');
                            }
                        )
                        ->get()
                        ->keyBy(fn ($row) => (string) $row->lead_id);

                    foreach ($leads as $lead) {
                        $bar->advance();

                        $key = (string) $lead->lead_id;
                        if (!isset($rows[$key])) {
                            $noMatch++;
                            continue;
                        }

                        $rec = $rows[$key];
                        if (empty($rec->recording_filename)) {
                            $noFilename++;
                            continue;
                        }

                        $base = preg_replace('/\.(mp3|wav|m4a|ogg)$/i', '', $rec->recording_filename);
                        $relPath = 'recordings/' . $base . '.mp3';

                        if ($verify && !$disk->exists($relPath)) {
                            $fileMissing++;
                            continue;
                        }

                        $localUrl = $appUrl . '/storage/' . $relPath;

                        if ($dryRun) {
                            $updated++;
                            continue;
                        }

                        try {
                            DB::table('avatar_leads')
                                ->where('id', $lead->id)
                                ->update([
                                    'recording_link' => $localUrl,
                                    'updated_at'     => now(),
                                ]);
                            $updated++;
                        } catch (\Exception $e) {
                            $errors++;
                            Log::error('SyncAvatarRecordingLinks update failed', [
                                'avatar_lead_id' => $lead->id,
                                'lead_id'        => $lead->lead_id,
                                'error'          => $e->getMessage(),
                            ]);
                        }
                    }

                    return true;
                },
                'id'
            );

        $bar->finish();
        $this->newLine(2);

        $this->info('Done.');
        $this->line("Updated: {$updated}");
        $this->line("No matching downloaded recording: {$noMatch}");
        $this->line("Matched but no recording_filename: {$noFilename}");
        $this->line("File missing in storage/public/recordings: {$fileMissing}");
        if ($errors > 0) {
            $this->warn("Errors: {$errors} (see laravel.log)");
        }

        return Command::SUCCESS;
    }
}
