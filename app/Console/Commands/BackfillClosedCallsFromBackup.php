<?php

namespace App\Console\Commands;

use App\Models\ClosedCall;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;

class BackfillClosedCallsFromBackup extends Command
{
    protected $signature = 'closed-calls:backfill-agentname
                            {--file=closedcall_backup.xlsx : Excel file inside public/}
                            {--sheet= : Optional sheet name}
                            {--limit= : Max number of DB rows to process}
                            {--dry-run : Show what would be updated without saving}';

    protected $description = 'Backfill closed_calls.agentname (NULL only) using Excel columns ID and Agent Name';

    public function handle(): int
    {
        $filePath = public_path($this->option('file'));
        if (!file_exists($filePath)) {
            $this->error("File not found: {$filePath}");
            return Command::FAILURE;
        }

        try {
            $excelRowsById = $this->loadExcelRowsById($filePath, $this->option('sheet'));
        } catch (\Throwable $e) {
            $this->error('Failed to read Excel file: ' . $e->getMessage());
            return Command::FAILURE;
        }

        if (empty($excelRowsById)) {
            $this->warn('No valid rows with id found in Excel file.');
            return Command::SUCCESS;
        }

        $query = ClosedCall::query()->whereNull('agentname');

        $limitOption = $this->option('limit');
        if ($limitOption !== null && $limitOption !== '') {
            $query->limit((int) $limitOption);
        }

        $candidates = $query->get();
        if ($candidates->isEmpty()) {
            $this->info('No closed_calls rows found with NULL agentname.');
            return Command::SUCCESS;
        }

        $dryRun = (bool) $this->option('dry-run');

        $matched = 0;
        $updated = 0;
        $noExcelIdMatch = 0;
        $sheetAgentNameEmpty = 0;
        $saveFailed = 0;

        foreach ($candidates as $closedCall) {
            $dbId = (string) $closedCall->id;
            if (!isset($excelRowsById[$dbId])) {
                $noExcelIdMatch++;
                continue;
            }

            $matched++;
            $excelData = $excelRowsById[$dbId];
            $excelAgentName = $excelData['agent_name'] ?? null;
            if ($excelAgentName === null || (is_string($excelAgentName) && trim($excelAgentName) === '')) {
                $sheetAgentNameEmpty++;
                continue;
            }

            $excelAgentName = is_string($excelAgentName) ? trim($excelAgentName) : $excelAgentName;

            if ($dryRun) {
                $updated++;
                continue;
            }

            try {
                $closedCall->agentname = $excelAgentName;
                $closedCall->save();
                $updated++;
            } catch (\Throwable $e) {
                $saveFailed++;
                Log::warning('Failed to backfill closed_call from backup', [
                    'id' => $closedCall->id,
                    'error' => $e->getMessage(),
                    'column' => 'agentname',
                ]);
            }
        }

        $this->info('Backfill complete.');
        $this->line("Candidates (agentname NULL): {$candidates->count()}");
        $this->line("Matched by id in Excel: {$matched}");
        $this->line("Rows " . ($dryRun ? 'that would be updated' : 'updated') . ": {$updated}");
        $this->line("Skipped (no ID match in sheet): {$noExcelIdMatch}");
        $this->line("Skipped (sheet Agent Name empty): {$sheetAgentNameEmpty}");
        $this->line("Save failed: {$saveFailed}");

        return Command::SUCCESS;
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function loadExcelRowsById(string $filePath, ?string $sheetName): array
    {
        $reader = IOFactory::createReader(IOFactory::identify($filePath));
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($filePath);

        $worksheet = $sheetName ? $spreadsheet->getSheetByName($sheetName) : $spreadsheet->getActiveSheet();
        if (!$worksheet) {
            $worksheet = $spreadsheet->getActiveSheet();
        }

        $highestRow = $worksheet->getHighestRow();
        $highestColumn = $worksheet->getHighestColumn();
        $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);

        $headerByColumn = [];
        for ($i = 1; $i <= $highestColumnIndex; $i++) {
            $col = Coordinate::stringFromColumnIndex($i);
            $rawHeader = $worksheet->getCell($col . '1')->getValue();
            if ($rawHeader === null || trim((string) $rawHeader) === '') {
                continue;
            }
            $rawHeaderString = (string) $rawHeader;
            $normalizedHeader = $this->normalizeHeader($rawHeaderString);
            $headerByColumn[$col] = $normalizedHeader;
        }

        $rowsById = [];
        for ($row = 2; $row <= $highestRow; $row++) {
            $rowData = [];
            foreach ($headerByColumn as $col => $header) {
                $value = $worksheet->getCell($col . $row)->getValue();
                $rowData[$header] = is_string($value) ? trim($value) : $value;
            }

            if (!isset($rowData['id'])) {
                continue;
            }

            $id = $this->normalizeIdValue($rowData['id']);
            if ($id === '') {
                continue;
            }

            $rowsById[$id] = $rowData;
        }

        return $rowsById;
    }

    private function normalizeHeader(string $header): string
    {
        $normalized = str_replace("\xEF\xBB\xBF", '', $header);
        $normalized = strtolower(trim($normalized));
        $normalized = preg_replace('/[^a-z0-9]+/', '_', $normalized);
        $normalized = trim($normalized, '_');

        return $normalized;
    }

    private function normalizeIdValue($value): string
    {
        if ($value === null) {
            return '';
        }

        if (is_numeric($value)) {
            return (string) (int) $value;
        }

        $stringValue = trim((string) $value);
        if ($stringValue === '') {
            return '';
        }

        if (preg_match('/^\d+(\.0+)?$/', $stringValue)) {
            return (string) (int) $stringValue;
        }

        return $stringValue;
    }
}
