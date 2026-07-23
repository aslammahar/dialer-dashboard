<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AttendancesTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('attendances')->insert([
            [
                'name' => 'John Doe',
                'employee_id' => 1,
                'uid' => 'EMP001',
                'state' => 'present',
                'attendance_time' => '08:00:00',
                'attendance_date' => '2024-09-17',
                'status' => 'active',
                'type' => 'check-in',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Jane Smith',
                'employee_id' => 2,
                'uid' => 'EMP002',
                'state' => 'present',
                'attendance_time' => '08:15:00',
                'attendance_date' => '2024-09-17',
                'status' => 'active',
                'type' => 'check-in',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Michael Johnson',
                'employee_id' => 3,
                'uid' => 'EMP003',
                'state' => 'absent',
                'attendance_time' => null,
                'attendance_date' => '2024-09-17',
                'status' => 'inactive',
                'type' => 'absence',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Emily Davis',
                'employee_id' => 4,
                'uid' => 'EMP004',
                'state' => 'present',
                'attendance_time' => '09:00:00',
                'attendance_date' => '2024-09-17',
                'status' => 'active',
                'type' => 'check-in',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
