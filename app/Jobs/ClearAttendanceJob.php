<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Exception;
use Rats\Zkteco\Lib\ZKTeco; // Assuming ZKLib is the SDK you're using. Adjust this based on your SDK.

class ClearAttendanceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        set_time_limit(300); // Set the maximum execution time to 300 seconds

        try {
            // Create a connection to the ZKTeco device
            $zk = new ZKTeco('192.168.18.43'); // Replace with the IP address of your device

            // Connect to the device
            $ret = $zk->connect();
            if ($ret) {
                // Clear the attendance logs
                $zk->clearAttendance();
                $zk->disconnect();
            } else {
                throw new Exception("Unable to connect to the device.");
            }
        } catch (Exception $e) {
            // Log the error or handle it as needed
            \Log::error("Failed to clear attendance: " . $e->getMessage());
        }
    }

}
