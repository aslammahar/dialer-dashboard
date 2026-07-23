<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AvatarLead;
use App\Models\Recording;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportRecordingLinksFromExcel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recordings:import-excel {--file=Rec_file.xlsx}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import recording links from Excel file and update avatar_leads.recording and recordings.recording_link columns';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting recording links import from Excel...');

        // Get the file path
        $fileName = $this->option('file');
        $filePath = public_path($fileName);

        if (!file_exists($filePath)) {
            $this->error("File not found: {$filePath}");
            $this->info("Please ensure the Excel file is in the public folder.");
            return Command::FAILURE;
        }

        $this->info("Reading Excel file: {$fileName}");

        // Read the Excel file
        try {
            $data = $this->readExcelFile($filePath);

            if (empty($data)) {
                $this->error("Excel file is empty or could not be read.");
                return Command::FAILURE;
            }

            // Process the data
            $this->info("Processing " . count($data) . " rows...");
            $this->newLine();

            $avatarLeadsUpdated = 0;
            $recordingsUpdated = 0;
            $recordingsCreated = 0;
            $avatarLeadsNotFound = 0;
            $errors = 0;

            $progressBar = $this->output->createProgressBar(count($data));
            $progressBar->start();

            foreach ($data as $row) {
                try {
                    $leadId = $this->getValue($row, 'lead_id');
                    $recordingLink = $this->getValue($row, 'recording_link');

                    if (empty($leadId)) {
                        $errors++;
                        $progressBar->advance();
                        continue;
                    }

                    if (empty($recordingLink)) {
                        $errors++;
                        $progressBar->advance();
                        continue;
                    }

                    $recordingLink = trim($recordingLink);

                    // Update avatar_leads.recording column
                    $avatarLead = AvatarLead::where('lead_id', $leadId)->first();

                    if ($avatarLead) {
                        $avatarLead->recording = $recordingLink;
                        // if QAstatus = no recording
                        if ($avatarLead->QAstatus === 'no recording') {
                            $avatarLead->QAstatus = 'pending';
                        }
                        $avatarLead->save();
                        $avatarLeadsUpdated++;

                        Log::info("Updated avatar_leads.recording for lead_id", [
                            'lead_id' => $leadId,
                            'recording' => $recordingLink
                        ]);
                    } else {
                        $avatarLeadsNotFound++;
                        Log::warning("AvatarLead not found for lead_id", [
                            'lead_id' => $leadId
                        ]);
                    }

                    // Update recordings.recording_link column, or create a new row if missing
                    $recording = Recording::where('lead_id', $leadId)->first();

                    if ($recording) {
                        $recording->recording_link = $recordingLink;
                        $recording->save();
                        $recordingsUpdated++;

                        Log::info("Updated recordings.recording_link for lead_id", [
                            'lead_id' => $leadId,
                            'recording_link' => $recordingLink
                        ]);
                    } else {
                        $newRecording = new Recording();
                        $newRecording->lead_id = $leadId;
                        $newRecording->recording_link = $recordingLink;
                        $newRecording->status = 'rec missing';
                        if (!empty($avatarLead)) {
                            $newRecording->recording_filename = $avatarLead->recording_filename ?: null;
                            $newRecording->server_ip = $avatarLead->server_ip ?: null;
                            $newRecording->dialer_id = $avatarLead->dialer_id ?: null;
                            $newRecording->dialer_name = $avatarLead->dialername ?: null;
                        }
                        $newRecording->save();
                        $recordingsCreated++;

                        Log::info("Created recordings row for lead_id", [
                            'lead_id' => $leadId,
                            'recording_link' => $recordingLink,
                            'status' => 'rec missing',
                        ]);
                    }
                } catch (\Exception $e) {
                    $errors++;
                    Log::error("Error processing row", [
                        'row' => $row,
                        'error' => $e->getMessage()
                    ]);
                }

                $progressBar->advance();
            }

            $progressBar->finish();
            $this->newLine(2);

            // Display results
            $this->info("Import Statistics:");
            $this->line("Total rows processed: " . count($data));
            $this->newLine();
            $this->line("Avatar Leads Table:");
            $this->line("  Successfully updated: {$avatarLeadsUpdated}");
            $this->line("  Not found: {$avatarLeadsNotFound}");
            $this->newLine();
            $this->line("Recordings Table:");
            $this->line("  Successfully updated: {$recordingsUpdated}");
            $this->line("  Newly created (status = rec missing): {$recordingsCreated}");
            $this->newLine();
            $this->line("Errors (empty lead_id or recording_link): {$errors}");

            $this->newLine();
            $this->info("Import completed successfully!");

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Error reading Excel file: " . $e->getMessage());
            Log::error("Excel import error", [
                'file' => $filePath,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return Command::FAILURE;
        }
    }

    /**
     * Read Excel file and return array of rows
     *
     * @param string $filePath
     * @return array
     */
    private function readExcelFile(string $filePath): array
    {
        $data = [];

        try {
            $spreadsheet = IOFactory::load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();
            $highestRow = $worksheet->getHighestRow();
            $highestColumn = $worksheet->getHighestColumn();

            // Get header row (first row)
            $headers = [];
            for ($col = 'A'; $col <= $highestColumn; $col++) {
                $cellValue = $worksheet->getCell($col . '1')->getValue();
                $headers[$col] = $this->normalizeHeader($cellValue);
            }

            // Read data rows (starting from row 2)
            for ($row = 2; $row <= $highestRow; $row++) {
                $rowData = [];
                $isEmpty = true;

                for ($col = 'A'; $col <= $highestColumn; $col++) {
                    $cellValue = $worksheet->getCell($col . $row)->getValue();
                    if ($cellValue !== null && trim($cellValue) !== '') {
                        $isEmpty = false;
                    }
                    $rowData[$headers[$col]] = $cellValue;
                }

                if (!$isEmpty) {
                    $data[] = $rowData;
                }
            }
        } catch (\Exception $e) {
            Log::error("Error reading Excel file", [
                'file' => $filePath,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }

        return $data;
    }

    /**
     * Normalize header name (case-insensitive, trim spaces, handle variations)
     *
     * @param mixed $header
     * @return string
     */
    private function normalizeHeader($header): string
    {
        if ($header === null) {
            return '';
        }

        $normalized = strtolower(trim($header));

        // Handle common variations
        $variations = [
            'lead_id' => ['leadid', 'lead id', 'lead-id', 'lead_id', 'leadid'],
            'recording_link' => ['recordinglink', 'recording link', 'recording links', 'recording-link', 'recording_link', 'recording url', 'recordingurl', 'recording_link', 'recording']
        ];

        foreach ($variations as $standard => $aliases) {
            if (in_array($normalized, array_merge([$standard], $aliases))) {
                return $standard;
            }
        }

        return $normalized;
    }

    /**
     * Get value from row array with case-insensitive key matching
     *
     * @param array $row
     * @param string $key
     * @return mixed
     */
    private function getValue(array $row, string $key)
    {
        // Try exact match first
        if (isset($row[$key])) {
            return $row[$key];
        }

        // Try case-insensitive match
        foreach ($row as $rowKey => $value) {
            if (strtolower($rowKey) === strtolower($key)) {
                return $value;
            }
        }

        return null;
    }
}
