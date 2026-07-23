<?php

namespace App\Http\Controllers;

use App\Models\AttendanceEmployee;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Employee;
use App\Models\IpRestrict;
use App\Models\User;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceEmployeeController extends Controller
{
    public function index(Request $request)
    {

        set_time_limit(300); // 5 minutes

        if (\Auth::user()->can('manage attendance')) {
    
            $employees = Employee::where('created_by', \Auth::user()->creatorId())->pluck('name', 'id');
            $employees->prepend('Select Employee', '');
    
            $attendanceEmployee = AttendanceEmployee::query();
    
            if ($request->has('from') && $request->has('to') && $request->has('employee')) {
                $from = $request->input('from');
                $to = $request->input('to');
                $employee = $request->input('employee');
    
                $attendanceEmployee->whereBetween('created_at', [$from, $to]);
                $attendanceEmployee->where('employee_id', $employee);
            }
    
            $attendanceEmployee = $attendanceEmployee->get();
    
            return view('attendance.index', compact('attendanceEmployee', 'employees'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        if (\Auth::user()->can('create attendance')) {
            $employees = User::where('created_by', '=', Auth::user()->creatorId())->where('type', '=', "employee")->get()->pluck('name', 'id');

            return view('attendance.create', compact('employees'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function store(Request $request)
    {
        if (\Auth::user()->can('create attendance')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'employee_id' => 'required',
                    'date' => 'required',
                    'clock_in' => 'required',
                    // Remove clock_out from validation for simple users
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $startTime = Utility::getValByName('company_start_time');
            $endTime = Utility::getValByName('company_end_time');

            $attendance = AttendanceEmployee::where('employee_id', '=', $request->employee_id)
                ->where('date', '=', $request->date)
                ->where('clock_out', '=', '00:00:00')
                ->get()->toArray();

            if ($attendance) {
                return redirect()->route('attendanceemployee.index')->with('error', __('Employee Attendance Already Created.'));
            } else {
                $date = date("Y-m-d");

                $totalLateSeconds = strtotime($request->clock_in) - strtotime($date . $startTime);
                $hours = floor($totalLateSeconds / 3600);
                $mins = floor($totalLateSeconds / 60 % 60);
                $secs = floor($totalLateSeconds % 60);
                $late = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);

                // Calculate clock-out time
                if (!\Auth::user()->can('manage attendance')) { // Check if user is not a management user
                    $clockOutTime = date('H:i:s', strtotime($request->clock_in) + 10 * 3600); // Add 10 hours
                } else {
                    $clockOutTime = $request->clock_out . ':00'; // Use provided clock-out time for management users
                }

                // Early Leaving calculation (if applicable)
                $totalEarlyLeavingSeconds = strtotime($date . $endTime) - strtotime($clockOutTime);
                $hours = floor($totalEarlyLeavingSeconds / 3600);
                $mins = floor($totalEarlyLeavingSeconds / 60 % 60);
                $secs = floor($totalEarlyLeavingSeconds % 60);
                $earlyLeaving = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);

                // Overtime calculation (if applicable)
                if (strtotime($clockOutTime) > strtotime($date . $endTime)) {
                    $totalOvertimeSeconds = strtotime($clockOutTime) - strtotime($date . $endTime);
                    $hours = floor($totalOvertimeSeconds / 3600);
                    $mins = floor($totalOvertimeSeconds / 60 % 60);
                    $secs = floor($totalOvertimeSeconds % 60);
                    $overtime = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);
                } else {
                    $overtime = '00:00:00';
                }

                // Create attendance record
                $employeeAttendance = new AttendanceEmployee();
                $employeeAttendance->employee_id = $request->employee_id;
                $employeeAttendance->date = $request->date;
                $employeeAttendance->status = 'Present';
                $employeeAttendance->clock_in = $request->clock_in . ':00';
                $employeeAttendance->clock_out = $clockOutTime; // Store calculated clock-out time
                $employeeAttendance->late = $late;
                $employeeAttendance->early_leaving = $earlyLeaving;
                $employeeAttendance->overtime = $overtime;
                $employeeAttendance->total_rest = '00:00:00';
                $employeeAttendance->created_by = \Auth::user()->creatorId();
                $employeeAttendance->save();

                return redirect()->route('attendanceemployee.index')->with('success', __('Employee attendance successfully created.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }



    public function edit($id)
    {
        if (\Auth::user()->can('edit attendance')) {
            $attendanceEmployee = AttendanceEmployee::where('id', $id)->first();
            $employees = Employee::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');

            return view('attendance.edit', compact('attendanceEmployee', 'employees'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }




    public function avatarVoiceAttendance(Request $request)
    {
        $employeeId = !empty(Auth::user()->employee) ? Auth::user()->employee->id : 0;
        $time = date("H:i:s");

        // Fetch company end time from the settings table
        $companyEndTime = \DB::table('settings')
            ->where('name', 'company_end_time')
            ->value('value'); // Assuming the end time is stored as 'H:i:s' in 'value'

        // For Avatar and Voice Users
        if (Auth::user()->hasRole(['Avatar', 'voice'])) {
            // Find today's attendance record
            $todayAttendance = AttendanceEmployee::where('employee_id', '=', $employeeId)
                ->where('date', date('Y-m-d'))
                ->first();

            // Check if the employee has already clocked in today
            if (empty($todayAttendance)) {
                // Create a new attendance record for avatar and voice users
                $attendanceEmployee = new AttendanceEmployee();
                $attendanceEmployee->employee_id = $employeeId;
                $attendanceEmployee->date = date('Y-m-d');
                $attendanceEmployee->clock_in = $time; // Set clock-in time
                $attendanceEmployee->clock_out = $companyEndTime; // Set clock-out time from settings
                $attendanceEmployee->status = 'Present'; // Set status
                $attendanceEmployee->created_by = Auth::user()->id; // Save creator
                $attendanceEmployee->save();

                return redirect()->back()->with('success', __('Avatar/Voice User Successfully Clocked In.'));
            } else {
                // Employee has already clocked in today, prevent another clock-in
                return redirect()->back()->with('error', __('Employee has already clocked in for today.'));
            }
        }

        return redirect()->back()->with('error', __('Unauthorized action.'));
    }




    public function attendance(Request $request)
{
    $employeeId = Auth::user()->employee->id ?? 0;
    $time = now()->setTimezone('Asia/Karachi')->format("H:i:s"); // Get current time in Asia/Karachi
    $currentDate = now()->setTimezone('Asia/Karachi')->format('Y-m-d'); // Get current date

    // Fetch today's attendance record for the user
    $todayAttendance = AttendanceEmployee::where('employee_id', $employeeId)
        ->where('date', $currentDate)
        ->latest()
        ->first();

    // Clock In Logic
    if ($request->input('in') !== null) {
        if ($todayAttendance && $todayAttendance->clock_out === null) {
            // User is already clocked in, prevent multiple clock-ins
            return redirect()->back()->with('error', __('Already clocked in. Please clock out first.'));
        } else {
            // No clock-in record found or already clocked out, create a new one
            $attendanceEmployee = new AttendanceEmployee();
            $attendanceEmployee->employee_id = $employeeId;
            $attendanceEmployee->date = $currentDate;
            $attendanceEmployee->clock_in = $time; // Store clock-in in Pakistani time
            $attendanceEmployee->clock_out = null; // Set clock-out to null
            $attendanceEmployee->status = 'Present';
            $attendanceEmployee->created_by = Auth::user()->id;

            // Save the record and ensure it has valid clock_in and date
            $attendanceEmployee->save();

            return redirect()->back()->with('success', __('Successfully Clocked In.'));
        }
    }

    // Clock Out Logic
    if ($request->input('out') !== null) {
        if ($todayAttendance && $todayAttendance->clock_out === null) {
            // User is clocking out for the first time today
            $todayAttendance->clock_out = $time; // Store clock-out in Pakistani time

            // Ensure valid clock-in and clock-out times exist before calculating total hours
            if (!empty($todayAttendance->clock_in) && !empty($todayAttendance->clock_out)) {
                // Calculate total hours worked
                $clockInTime = new \DateTime($todayAttendance->clock_in);
                $clockOutTime = new \DateTime($time);
                $interval = $clockInTime->diff($clockOutTime);
                $totalHours = $interval->format('%h:%i:%s');

                // Update record with total hours worked
                $todayAttendance->total_hours = $totalHours;
            }

            // Save the updated record
            $todayAttendance->save();

            return redirect()->back()->with('success', __('Successfully Clocked Out. Total Hours Worked: ' . $totalHours));
        } else {
            // User has already clocked out or has no clock-in record
            return redirect()->back()->with('error', __('You need to clock in before clocking out.'));
        }
    }

    // Default response in case no valid action is caught
    return redirect()->back()->with('error', __('Invalid action.'));
}




    public function update(Request $request, $id)
    {
        \Log::info('Update function called with ID: ' . $id); // Log the call

        $employeeId = !empty(\Auth::user()->employee) ? \Auth::user()->employee->id : 0;
        $time = date("H:i:s");

        // For Management Users
        if (Auth::user()->can('management')) {
            // Find the attendance record that needs to be clocked out
            $todayAttendance = AttendanceEmployee::where('id', $id)->first();

            if ($todayAttendance) {
                // If clock_out is still '00:00:00', update it
                if ($todayAttendance->clock_out == '00:00:00') {
                    $todayAttendance->clock_out = $time; // Set clock-out time
                    $todayAttendance->save();

                    // Optionally, calculate total hours worked
                    $totalHours = $this->calculateTotalHoursWorked($employeeId, date('Y-m-d'));
                    $todayAttendance->total_hours = $totalHours; // Save to DB if necessary
                    $todayAttendance->save();

                    return redirect()->back()->with('success', __('Successfully Clocked Out. Total Hours Worked: ' . $totalHours));
                } else {
                    return redirect()->back()->with('error', __('You have already clocked out for this record.'));
                }
            } else {
                return redirect()->back()->with('error', __('No attendance record found.'));
            }
        } else {
            return redirect()->back()->with('error', __('Access Denied.'));
        }
    }


    public function calculateTotalHoursWorked($employeeId, $date)
    {
        $attendances = AttendanceEmployee::where('employee_id', '=', $employeeId)
            ->where('date', '=', $date)
            ->get();

        $totalSeconds = 0;

        foreach ($attendances as $attendance) {
            if ($attendance->clock_out != '00:00:00') {
                $clockInTime = strtotime($attendance->clock_in);
                $clockOutTime = strtotime($attendance->clock_out);
                $totalSeconds += ($clockOutTime - $clockInTime);
            }
        }

        $totalHours = gmdate("H:i:s", $totalSeconds);
        return $totalHours;
    }







    public function bulkAttendance(Request $request)
    {
        if (\Auth::user()->can('create attendance')) {

            $branch = Branch::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $branch->prepend('Select Branch', '');

            $department = Department::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $department->prepend('Select Department', '');

            $employees = [];
            if (!empty($request->branch) && !empty($request->department)) {
                $employees = Employee::where('created_by', \Auth::user()->creatorId())->where('branch_id', $request->branch)->where('department_id', $request->department)->get();
            } else {
                $employees = Employee::where('created_by', \Auth::user()->creatorId())->where('branch_id', 1)->where('department_id', 1)->get();
            }


            return view('attendance.bulk', compact('employees', 'branch', 'department'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function bulkAttendanceData(Request $request)
    {

        if (\Auth::user()->can('create attendance')) {
            if (!empty($request->branch) && !empty($request->department)) {
                $startTime = Utility::getValByName('company_start_time');
                $endTime = Utility::getValByName('company_end_time');
                $date = $request->date;

                $employees = $request->employee_id;
                $atte = [];

                if (!empty($employees)) {
                    foreach ($employees as $employee) {
                        $present = 'present-' . $employee;
                        $in = 'in-' . $employee;
                        $out = 'out-' . $employee;
                        $atte[] = $present;
                        if ($request->$present == 'on') {

                            $in = date("H:i:s", strtotime($request->$in));
                            $out = date("H:i:s", strtotime($request->$out));

                            $totalLateSeconds = strtotime($in) - strtotime($startTime);

                            $hours = floor($totalLateSeconds / 3600);
                            $mins = floor($totalLateSeconds / 60 % 60);
                            $secs = floor($totalLateSeconds % 60);
                            $late = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);

                            //early Leaving
                            $totalEarlyLeavingSeconds = strtotime($endTime) - strtotime($out);
                            $hours = floor($totalEarlyLeavingSeconds / 3600);
                            $mins = floor($totalEarlyLeavingSeconds / 60 % 60);
                            $secs = floor($totalEarlyLeavingSeconds % 60);
                            $earlyLeaving = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);

                            if (strtotime($out) > strtotime($endTime)) {
                                //Overtime
                                $totalOvertimeSeconds = strtotime($out) - strtotime($endTime);
                                $hours = floor($totalOvertimeSeconds / 3600);
                                $mins = floor($totalOvertimeSeconds / 60 % 60);
                                $secs = floor($totalOvertimeSeconds % 60);
                                $overtime = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);
                            } else {
                                $overtime = '00:00:00';
                            }


                            $attendance = AttendanceEmployee::where('employee_id', '=', $employee)->where('date', '=', $request->date)->first();

                            if (!empty($attendance)) {
                                $employeeAttendance = $attendance;
                            } else {
                                $employeeAttendance = new AttendanceEmployee();
                                $employeeAttendance->employee_id = $employee;
                                $employeeAttendance->created_by = \Auth::user()->creatorId();
                            }


                            $employeeAttendance->date = $request->date;
                            $employeeAttendance->status = 'Present';
                            $employeeAttendance->clock_in = $in;
                            $employeeAttendance->clock_out = $out;
                            $employeeAttendance->late = $late;
                            $employeeAttendance->early_leaving = ($earlyLeaving > 0) ? $earlyLeaving : '00:00:00';
                            $employeeAttendance->overtime = $overtime;
                            $employeeAttendance->total_rest = '00:00:00';
                            $employeeAttendance->save();
                        } else {
                            $attendance = AttendanceEmployee::where('employee_id', '=', $employee)->where('date', '=', $request->date)->first();

                            if (!empty($attendance)) {
                                $employeeAttendance = $attendance;
                            } else {
                                $employeeAttendance = new AttendanceEmployee();
                                $employeeAttendance->employee_id = $employee;
                                $employeeAttendance->created_by = \Auth::user()->creatorId();
                            }

                            $employeeAttendance->status = 'Leave';
                            $employeeAttendance->date = $request->date;
                            $employeeAttendance->clock_in = '00:00:00';
                            $employeeAttendance->clock_out = '00:00:00';
                            $employeeAttendance->late = '00:00:00';
                            $employeeAttendance->early_leaving = '00:00:00';
                            $employeeAttendance->overtime = '00:00:00';
                            $employeeAttendance->total_rest = '00:00:00';
                            $employeeAttendance->save();
                        }
                    }
                } else {
                    return redirect()->back()->with('error', __('Employee not found.'));
                }


                return redirect()->back()->with('success', __('Employee attendance successfully created.'));
            } else {
                return redirect()->back()->with('error', __('Branch & department field required.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }




    public function destroy($id)
    {
        if (\Auth::user()->can('delete attendance')) {
            $attendance = AttendanceEmployee::where('id', $id)->first();

            $attendance->delete();

            return redirect()->route('attendanceemployee.index')->with('success', __('Attendance successfully deleted.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
