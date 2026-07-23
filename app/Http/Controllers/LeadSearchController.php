<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class LeadSearchController extends Controller
{
    private const USERNAME = "CRM302";
    private const PASSWORD = "EpDWb270J3";

    public function index(Request $request)
    {
        // Execute the multi-cURL logic first
        $responses = $this->performMultiCurlRequests();

        // Check responses and log status
        foreach ($responses as $key => $status) {
            \Log::info("Request $key: $status");
        }

        // Optionally, check the response to determine next behavior
        if (in_array('Error', $responses)) {
            return redirect()->back()->with('error', 'Some requests failed. Please try again.');
        }

        // Redirect or render the form page after logic execution
        return view('search_leads.form', [
            
            'begin_date' => Carbon::now()->subDays(3)->format('Y-m-d'),
            'end_date' => Carbon::now()->format('Y-m-d')
        ]);
    }

    private function performMultiCurlRequests()
    {
        $requests = [
            [
                'url' => 'https://jsons2.dialerhosting.com:81/jS0n2acceSs.php?VD_login=EMP0000580&VD_pass=hello123'
            ],
            [
                'url' => 'https://json6.dialerhosting.com:81/json6Access.php?VD_login=EMP0000580&VD_pass=hello123'
            ],
            [
                'url' => 'http://jsonarchive.dialerhosting.com:81/access.php?VD_login=6666&VD_pass=hello123'
            ],
            [
                'url' => 'https://jsons4.dialerhosting.com:446/Ch3cKV@lid@T0r.php',
                'data' => [
                    'Jzr87Cp8XqJY' => 'EMP0000580',
                    'WNK1WOrAvT1I' => 'hello123',
                    'submit' => 'Submit'
                ]
            ]
        ];

        $multiHandle = curl_multi_init();
        $curlHandles = [];

        foreach ($requests as $key => $request) {
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $request['url']);
            if (isset($request['data'])) {
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($request['data']));
            }
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
            curl_setopt($ch, CURLOPT_NOBODY, false);

            curl_multi_add_handle($multiHandle, $ch);
            $curlHandles[$key] = $ch;
        }

        $running = null;
        do {
            curl_multi_exec($multiHandle, $running);
            curl_multi_select($multiHandle);
        } while ($running > 0);

        $responses = [];
        foreach ($curlHandles as $key => $ch) {
            if (curl_errno($ch)) {
                $responses[$key] = 'Error';
            } else {
                $responses[$key] = 'Working';
            }
            curl_multi_remove_handle($multiHandle, $ch);
            curl_close($ch);
        }

        curl_multi_close($multiHandle);

        return $responses;
    }

    public function searchLeadRecording(Request $request)
    {
        set_time_limit(300); // Increase execution time

        // Validate input
        $validatedData = $request->validate([
            'lead_id' => 'required|string',
            'user' => 'required|string',
            'begin_date' => 'required|date',
            'end_date' => 'required|date'
        ]);

        if (!auth()->user()->can('search lead recording')) {
            return redirect()->back()->with('error', 'Unauthorized.');
        }

        $searchLead = $validatedData['lead_id'];
        $user = strtoupper(trim($validatedData['user']));
        $beginDate = $validatedData['begin_date'];
        $endDate = $validatedData['end_date'];

        // Get URLs based on user type
        $urls = $this->getUrls($user, $beginDate, $endDate);

        // Process the URLs to find the lead
        foreach ($urls as $url) {
            $recordingLink = $this->fetchAndProcessFile($url, $searchLead, $user);

            if ($recordingLink) {
                return redirect()->away($recordingLink);
            }
        }

        return back()->with('error', "LEAD {$searchLead} not found in any files.");
    }

    private function getUrls($user, $beginDate, $endDate)
    {
        if (str_starts_with($user, 'SLZ')) {
            return [
                "https://json6.dialerhosting.com/Jsab6q/user_stats.php?DB=&pause_code_rpt=&park_rpt=&did_id=&did=&begin_date={$beginDate}&end_date={$endDate}&user={$user}&submit=submit&search_archived_data=checked&NVAuser=&file_download=8"
            ];
        } else {
            return [
                "https://jsons2.dialerhosting.com/di35adYtrw/user_stats.php?DB=&pause_code_rpt=&park_rpt=&did_id=&did=&begin_date={$beginDate}&end_date={$endDate}&user={$user}&submit=&search_archived_data=checked&NVAuser=&file_download=8",
                "https://jsons4.dialerhosting.com/4ii52TAR1/user_stats.php?DB=&pause_code_rpt=&park_rpt=&did_id=&did=&begin_date={$beginDate}&end_date={$endDate}&user={$user}&submit=&search_archived_data=checked&NVAuser=&file_download=8"
            ];
        }
    }

    private function fetchAndProcessFile($url, $searchLead, $user)
    {
        try {
            // Fetch file content
            $response = Http::withBasicAuth(self::USERNAME, self::PASSWORD)
                ->get($url);

            if (!$response->successful()) {
                Log::error("Failed to fetch file", ['url' => $url, 'status' => $response->status()]);
                return null;
            }

            $fileContent = $response->body();

            // Process the file content
            return $this->findLeadInContent($fileContent, $searchLead, $user);
        } catch (\Exception $e) {
            Log::error("Error fetching file", ['url' => $url, 'error' => $e->getMessage()]);
            return null;
        }
    }

    private function findLeadInContent($fileContent, $searchLead, $user)
{
    $tempFile = tmpfile();

    // Ensure the temporary file was successfully created
    if (!$tempFile) {
        Log::error("Unable to create a temporary file.");
        return null;
    }

    fwrite($tempFile, $fileContent);
    fseek($tempFile, 0);

    try {
        // Skip header rows
        fgetcsv($tempFile, 0, ",");
        $header = array_map('trim', fgetcsv($tempFile, 0, ","));

        // Determine LEAD and FILENAME/LOCATION columns
        $leadIndex = array_search('LEAD', $header);
        $locationIndex = array_search($this->getFileColumn($user), $header);

        if ($leadIndex === false || $locationIndex === false) {
            Log::error("Required columns not found", ['header' => $header]);
            return null;
        }

        // Search through rows
        while (($row = fgetcsv($tempFile, 0, ",")) !== false) {
            if (isset($row[$leadIndex]) && $row[$leadIndex] == $searchLead) {
                $recordingLink = $row[$locationIndex] ?? '';

                if (!empty($recordingLink) && strpos($recordingLink, "https://jsonarchive.dialerhosting.com") === 0) {
                    return $recordingLink; // Return recording link
                }
            }
        }

    } catch (\Exception $e) {
        Log::error("Error processing file content", ['error' => $e->getMessage()]);
    } finally {
        // Close file only if it is valid
        if (is_resource($tempFile)) {
            fclose($tempFile);
        }
    }

    return null;
}


    private function getFileColumn($user)
    {
        // For SLZ users, the column is LOCATION. For others, it's FILENAME.
        return str_starts_with($user, 'SLZ') ? 'LOCATION' : 'FILENAME';
    }
}
