<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Http\Requests\ScheduleEmp;
use App\Models\User;
use Illuminate\Http\Request; 
// schedule_employees
class ScheduleController extends Controller
{
   
    public function index()
    {
     
        return view('admin.schedule')->with('schedules', Schedule::all());
        flash()->success('Success','Schedule has been created successfully !');

    }


    public function store(ScheduleEmp $request)
    {
        $request->validated();

        $schedule = new schedule;
        $schedule->slug = $request->slug;
        $schedule->time_in = $request->time_in;
        $schedule->time_out = $request->time_out;
        $schedule->save();




        flash()->success('Success','Schedule has been created successfully !');
        return redirect()->route('schedule.index');

    }

    public function update(ScheduleEmp $request, Schedule $schedule)
    {
        $request['time_in'] = str_split($request->time_in, 5)[0];
        $request['time_out'] = str_split($request->time_out, 5)[0];

        $request->validated();

        $schedule->slug = $request->slug;
        $schedule->time_in = $request->time_in;
        $schedule->time_out = $request->time_out;
        $schedule->save();
        flash()->success('Success','Schedule has been Updated successfully !');
        return redirect()->route('schedule.index');


    }

  
    public function destroy(Schedule $schedule)
    {
        $schedule->delete();
        flash()->success('Success','Schedule has been deleted successfully !');
        return redirect()->route('schedule.index');
    }


    // get all users to assign schedule
    public function showAssignScheduleForm(Request $request)
    {
        $type = $request->input('type');
        $types = User::distinct()->pluck('type'); // Get all unique user types
        $usersQuery = User::query();
    
        if ($type) {
            $usersQuery->where('type', $type);
        }
    
        $users = $usersQuery->get();
        $schedules = Schedule::all();
        return view('attendance.schedule_time', compact('users', 'schedules', 'types'));
    }

    public function assignSchedule(Request $request)
    {
        $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id'
        ]);

        $scheduleId = $request->input('schedule_id');
        $userIds = $request->input('user_ids');

        User::whereIn('id', $userIds)->update(['schedule_id' => $scheduleId]);

        return redirect()->route('assign.schedule.form')->with('success', 'Schedule assigned to selected users.');
    }

}
