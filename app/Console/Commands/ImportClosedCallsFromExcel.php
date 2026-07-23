<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ClosedCall;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class ImportClosedCallsFromExcel extends Command
{
    protected $signature = 'closed-calls:import-from-excel 
                            {--file=FE Closed Sales (J24) 4.0_with_closer_id.xlsx : Excel file in public folder}
                            {--sheet=Form Responses 1XDFC : Sheet name}
                            {--memory=1G : Memory limit}
                            {--limit= : Max rows to import (default: all)}
                            {--dry-run : Only show mapping and row count, do not insert}';

    protected $description = 'Import closed calls from Excel into closed_calls table (Timestamp -> created_at, other columns mapped)';

    /** Excel header (normalized) => closed_calls column */
    protected function getHeaderToColumnMap(): array
    {
        return [
            'timestamp' => 'created_at',
            'customer_name' => 'customer_full_name',
            'phone_number' => 'phone_number',
            'alternate_phone_number' => 'alternate_phone_number',
            'alt_phone' => 'alternate_phone_number',
            'email' => 'cx_email',
            'address' => 'address',
            'city' => 'city',
            'state' => 'state',
            'zip_code' => 'zip_code',
            'zip' => 'zip_code',
            'gender' => 'gender',
            'martial_status' => 'martial_status',
            'marital_status' => 'martial_status',
            'age' => 'age',
            'dob' => 'dob',
            'date_of_birth' => 'dob',
            'place_of_birth' => 'palce_of_birth',
            'palce_of_birth' => 'palce_of_birth',
            'height' => 'height',
            'weight' => 'weight',
            'social_security' => 'social_security',
            'smoker' => 'smoker',
            'health_condition' => 'health_condition',
            'medication' => 'medication',
            'hospital_name' => 'hospital_name',
            'hospital_address' => 'hospital_address',
            'physician_name' => 'physician_name',
            'monthly_premium' => 'monthly_premium',
            'carrier' => 'carrier',
            'coverage_plan' => 'coverage_plan',
            'customer_eligibility' => 'customer_eligibility',
            'beneficiary' => 'beneficiary',
            'beneficiary_relation' => 'beneficiary_relation',
            'beneficiary_phone' => 'beneficiary_phone',
            'beneficiary_dob' => 'beneficiary_dob',
            'payor' => 'payor',
            'bank_name' => 'bank_name',
            'bank_address' => 'bank_address',
            'routing_number' => 'routing_number',
            'bank_account_number' => 'bank_account_number',
            'debit_card_no' => 'debit_card_direct_express_no',
            'debit_card' => 'debit_card_direct_express_no',
            'debit_expiration' => 'debit_card_direct_express_expiration',
            'debit_cvv' => 'debit_card_direct_express_cvv',
            'account_type' => 'account_type',
            'initial_draft_date' => 'initial_draft_date',
            'future_draft_date' => 'future_draft_date',
            'underwriter_name' => 'underwriter_name',
            'remarks' => 'remarks',
            'closer_id' => 'closer_id',
            'closer_name' => 'closername',
            'junior_closer_name' => 'junior_closer_name',
            'center_name' => 'center_name',
            'sale_made_by' => 'sale_made_by',
            'status' => 'status',
            'clients_comment' => 'clients_comment',
            'client_comment' => 'clients_comment',
            'clients_id' => 'clients_id',
            'client_id' => 'clients_id',
            'juniorcloser2' => 'juniorcloser2',
            'junior_closer_2' => 'juniorcloser2',
            'lead_id' => 'lead_id',
            'teamname' => 'teamname',
            'team_name' => 'teamname',
            'agentname' => 'agentname',
            'agent_name' => 'agentname',
            'dialeragentname' => 'dialeragentname',
            'dialer_agent_name' => 'dialeragentname',
            'dialername' => 'dialername',
            'dialer_name' => 'dialername',
            'list_id_2' => 'list_id_2',
            'list_id_1' => 'list_id_1',
            'recording_id' => 'recording_id',
            'hippa_id' => 'hippa_id',
            'hipaa_id' => 'hippa_id',
            'policy_id' => 'policy_id',
            'signature_type' => 'signature_type',
            'call_id' => 'call_id',
            'dialer_name_new' => 'dialer_name_new',
            'client_name_2' => 'client_name_2',
            'agent_status' => 'agent_status',
        ];
    }

    public function handle()
    {
        $fileName = $this->option('file');
        $filePath = public_path($fileName);

        if (!file_exists($filePath)) {
            $this->error("File not found: {$filePath}");
            return Command::FAILURE;
        }

        $memoryLimit = $this->option('memory') ?: '1G';
        ini_set('memory_limit', $memoryLimit);

        $this->info("Loading: {$fileName}");

        try {
            $reader = IOFactory::createReader(IOFactory::identify($filePath));
            $reader->setReadDataOnly(true);
            $reader->setReadEmptyCells(false);
            $spreadsheet = $reader->load($filePath);

            $sheetName = $this->option('sheet');
            $worksheet = $sheetName ? $spreadsheet->getSheetByName($sheetName) : $spreadsheet->getActiveSheet();
            if (!$worksheet) {
                $worksheet = $spreadsheet->getActiveSheet();
                $this->warn("Sheet '{$sheetName}' not found, using active sheet.");
            }
            $this->info("Sheet: " . $worksheet->getTitle());

            $highestRow = $worksheet->getHighestRow();
            $highestColumn = $worksheet->getHighestColumn();
            $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);

            // Build header index -> db column
            $map = $this->getHeaderToColumnMap();
            $headerToDb = [];
            for ($i = 1; $i <= $highestColumnIndex; $i++) {
                $col = Coordinate::stringFromColumnIndex($i);
                $raw = $worksheet->getCell($col . '1')->getValue();
                if ($raw === null || trim((string) $raw) === '') {
                    continue;
                }
                $normalized = $this->normalizeHeader((string) $raw);
                if (isset($map[$normalized])) {
                    $headerToDb[$col] = $map[$normalized];
                }
            }

            $allowedColumns = array_merge($this->getFillable(), ['created_at', 'updated_at']);
            $headerToDb = array_filter($headerToDb, fn ($dbCol) => in_array($dbCol, $allowedColumns, true));

            if (empty($headerToDb)) {
                $this->error('No columns could be mapped. Check header names.');
                return Command::FAILURE;
            }

            $this->info('Mapped ' . count($headerToDb) . ' columns.');
            $limitOption = $this->option('limit');
            $limit = $limitOption !== null && $limitOption !== '' ? (int) $limitOption : null;
            $totalRows = $highestRow - 1;
            if ($limit !== null && $limit > 0) {
                $totalRows = min($totalRows, $limit);
            }

            if ($this->option('dry-run')) {
                $this->info('Dry run: would import up to ' . $totalRows . ' rows.');
                $rows = [];
                foreach ($headerToDb as $col => $db) {
                    $rows[] = [$col, $db];
                }
                $this->table(['Excel col', 'DB column'], $rows);
                return Command::SUCCESS;
            }

            $inserted = 0;
            $failed = 0;
            $batchSize = 500;
            $progressBar = $this->output->createProgressBar($totalRows);
            $progressBar->start();

            for ($row = 2; $row <= $highestRow; $row++) {
                if ($limit !== null && $inserted + $failed >= $limit) {
                    break;
                }

                $attrs = [];
                foreach ($headerToDb as $col => $dbCol) {
                    $value = $worksheet->getCell($col . $row)->getValue();
                    if ($value === null || (is_string($value) && trim($value) === '')) {
                        continue;
                    }
                    $value = is_string($value) ? trim($value) : $value;

                    if ($dbCol === 'created_at' || $dbCol === 'updated_at') {
                        $parsed = $this->parseTimestamp($value);
                        if ($parsed) {
                            $attrs[$dbCol] = $parsed;
                        }
                        continue;
                    }

                    if (in_array($dbCol, ['dob', 'beneficiary_dob', 'initial_draft_date', 'future_draft_date'], true)) {
                        $parsed = $this->parseDate($value);
                        $attrs[$dbCol] = $parsed ?? $value;
                    } else {
                        $attrs[$dbCol] = $value;
                    }
                }

                if (empty($attrs)) {
                    $progressBar->advance();
                    continue;
                }

                if (!isset($attrs['created_at'])) {
                    $attrs['created_at'] = now();
                }
                if (!isset($attrs['updated_at'])) {
                    $attrs['updated_at'] = $attrs['created_at'];
                }
                // closed_calls.closer_id is NOT NULL; default to 0 if missing
                if (!isset($attrs['closer_id']) || $attrs['closer_id'] === '') {
                    $attrs['closer_id'] = 0;
                }

                // Only pass fillable + timestamps
                $fillable = array_merge($this->getFillable(), ['created_at', 'updated_at']);
                $attrs = array_intersect_key($attrs, array_flip($fillable));

                try {
                    ClosedCall::create($attrs);
                    $inserted++;
                } catch (\Throwable $e) {
                    $failed++;
                    Log::warning('Import closed call row failed', ['row' => $row, 'error' => $e->getMessage(), 'attrs_keys' => array_keys($attrs)]);
                }

                $progressBar->advance();
            }

            $progressBar->finish();
            $this->newLine(2);
            $this->info("Import finished. Inserted: {$inserted}, Failed: {$failed}");

            Log::info('Closed calls import completed', ['file' => $fileName, 'inserted' => $inserted, 'failed' => $failed]);
            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $this->error($e->getMessage());
            Log::error('Closed calls import failed', ['file' => $fileName, 'error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return Command::FAILURE;
        }
    }

    private function getFillable(): array
    {
        return (new ClosedCall)->getFillable();
    }

    private function normalizeHeader(string $header): string
    {
        $s = preg_replace('/\s+/', '_', strtolower(trim($header)));
        return preg_replace('/[^a-z0-9_]/', '', $s);
    }

    private function parseTimestamp($value): ?string
    {
        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d H:i:s');
        }
        if (is_numeric($value)) {
            try {
                $dt = ExcelDate::excelToDateTimeObject($value);
                return $dt->format('Y-m-d H:i:s');
            } catch (\Throwable $e) {
                return null;
            }
        }
        $str = trim((string) $value);
        if ($str === '') {
            return null;
        }
        // US format in sheet: 2/1/2024 22:23:48 (month/day/year hour:minute:second)
        $formats = [
            'n/j/Y H:i:s',   // 2/1/2024 22:23:48
            'm/d/Y H:i:s',   // 02/01/2024 22:23:48
            'n/j/Y G:i:s',   // 2/1/2024 9:05:00 (hour no leading zero)
            'm/d/Y G:i:s',
            'n/j/Y H:i',     // no seconds
            'm/d/Y H:i',
            'Y-m-d H:i:s',   // 2024-02-01 22:23:48
        ];
        foreach ($formats as $format) {
            $dt = Carbon::createFromFormat($format, $str);
            if ($dt !== false) {
                return $dt->format('Y-m-d H:i:s');
            }
        }
        try {
            return Carbon::parse($str)->format('Y-m-d H:i:s');
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function parseDate($value): ?string
    {
        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d');
        }
        if (is_numeric($value)) {
            try {
                $dt = ExcelDate::excelToDateTimeObject($value);
                return $dt->format('Y-m-d');
            } catch (\Throwable $e) {
                return null;
            }
        }
        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }
}
