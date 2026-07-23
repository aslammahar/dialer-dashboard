<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Leave;
use App\Models\LeaveType;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class LeaveController extends Controller
{
    public function index()
{
    $user = \Auth::user();

    // Check if the user has the permission to approve leaves
    if ($user->can('approve leave')) {
        // Fetch all leaves created by the user's creator (admin)
        $leaves = Leave::all();
    } else {
        // Fetch only the leaves related to the logged-in employee
        $employee = Employee::where('user_id', '=', $user->id)->first();
        $leaves = Leave::where('employee_id', '=', $employee->id)->get();
    }

    return view('leave.index', compact('leaves'));
}


public function create()
{
    if (Auth::user()->type == 'employee') {
        $employees = Employee::where('user_id', Auth::user()->id)
            ->orderBy('name', 'asc') // Sorting employees A to Z
            ->pluck('name', 'id');
    } else {
        $employees = Employee::where('created_by', Auth::user()->creatorId())
            ->orderBy('name', 'asc') // Sorting employees A to Z
            ->pluck('name', 'id');
    }

    $leavetypes = LeaveType::where('created_by', Auth::user()->creatorId())->get();
    $leavetypes_days = LeaveType::where('created_by', Auth::user()->creatorId())->get();

    return view('leave.create', compact('employees', 'leavetypes', 'leavetypes_days'));
}

   
// public function store(Request $request)
// {
//     if (\Auth::user()) {
//         $validator = \Validator::make(
//             $request->all(), [
//                 'employee_id' => 'required', // Ensure the employee is selected
//                 'leave_type_id' => 'required',
//                 'start_date' => 'required|date',
//                 'end_date' => 'required|date|after_or_equal:start_date',
//                 'leave_reason' => 'required',
//                 'remark' => 'required',
//             ]
//         );

//         if ($validator->fails()) {
//             return redirect()->back()->with('error', $validator->getMessageBag()->first());
//         }

//         // Get the employee ID (Either their own or the selected one)
//         if (\Auth::user()->type == 'employee') {
//             $employee = Employee::where('user_id', \Auth::user()->id)->first();
//         } else {
//             $employee = Employee::find($request->employee_id);
//         }

//         if (!$employee) {
//             return redirect()->back()->with('error', __('Invalid employee.'));
//         }

//         $leaveType = LeaveType::find($request->leave_type_id);
//         if (!$leaveType) {
//             return redirect()->back()->with('error', __('Invalid leave type.'));
//         }

//         // Calculate total leave days
//         $startDate = new \DateTime($request->start_date);
//         $endDate = new \DateTime($request->end_date);
//         $totalLeaveDays = $startDate->diff($endDate)->days + 1;

//         // Check if the employee has enough leave balance
//         $usedLeaves = Leave::where('employee_id', $employee->id)
//             ->where('leave_type_id', $request->leave_type_id)
//             ->where('status', 'Approved')
//             ->sum('total_leave_days');

//         $remainingLeaves = $leaveType->days - $usedLeaves;

//         if ($totalLeaveDays > $remainingLeaves) {
//             return redirect()->back()->with('error', __('Not enough leave balance. Available: ') . $remainingLeaves);
//         }

//         // Save the leave request
//         $leave = new Leave();
//         $leave->employee_id = $employee->id;
//         $leave->leave_type_id = $request->leave_type_id;
//         $leave->applied_on = now()->format('Y-m-d');
//         $leave->start_date = $request->start_date;
//         $leave->end_date = $request->end_date;
//         $leave->total_leave_days = $totalLeaveDays;
//         $leave->leave_reason = $request->leave_reason;
//         $leave->other_leave = $request->other_leave;
//         $leave->remark = $request->remark;
//         $leave->status = 'Pending';
//         $leave->created_by = \Auth::user()->creatorId();

//         $leave->save();

//         return redirect()->route('leave.index')->with('success', __('Leave successfully created.'));
//     } else {
//         return redirect()->back()->with('error', __('Permission denied.'));
//     }
// }


public function store(Request $request)
{
    if (\Auth::user()) {
        $validator = \Validator::make(
            $request->all(),
            [
                'leave_type_id' => 'required',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'leave_reason' => 'required',
                'remark' => 'required',
            ]
        );

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->getMessageBag()->first());
        }

        // Determine the employee
        if ($request->filled('employee_id')) {
            $employee = \App\Models\Employee::find($request->employee_id);
        } else {
            $employee = \App\Models\Employee::where('user_id', \Auth::user()->id)->first();
        }

        // If no employee found, return an error
        if (!$employee) {
            return redirect()->back()->with('error', __('Employee record not found.'));
        }

        // Create leave record
        $leave = new Leave();
        $leave->employee_id = $employee->id;
        $leave->leave_type_id = $request->leave_type_id;
        $leave->applied_on = now()->format('Y-m-d');
        $leave->start_date = $request->start_date;
        $leave->end_date = $request->end_date;
        $leave->total_leave_days = 0; // You may need logic to calculate this
        $leave->leave_reason = $request->leave_reason;
        $leave->other_leave = $request->other_leave; // Optional field
        $leave->remark = $request->remark;
        $leave->status = 'Pending';
        $leave->created_by = \Auth::user()->id; // Store who created the leave

        if ($leave->save()) {
            return redirect()->route('leave.index')->with('success', __('Leave successfully created.'));
        } else {
            return redirect()->back()->with('error', __('Failed to create leave.'));
        }
    }

    return redirect()->back()->with('error', __('Permission denied.'));
}


    public function show(Leave $leave)
    {
        return redirect()->route('leave.index');
    }

    public function edit(Leave $leave)
    {
        if(\Auth::user()->can('edit leave'))
        {
            if($leave->created_by == \Auth::user()->creatorId())
            {
                $employees  = Employee::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
                $leavetypes = LeaveType::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('title', 'id');

                return view('leave.edit', compact('leave', 'employees', 'leavetypes'));
            }
            else
            {
                return response()->json(['error' => __('Permission denied.')], 401);
            }
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function update(Request $request, $leave)
    {

        $leave = Leave::find($leave);
        if(\Auth::user()->can('edit leave'))
        {
            if($leave->created_by == Auth::user()->creatorId())
            {
                $validator = \Validator::make(
                    $request->all(), [
                        'leave_type_id' => 'required',
                        'start_date' => 'required',
                        'end_date' => 'required',
                        'leave_reason' => 'required',
                        'remark' => 'required',
                    ]
                );
                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }

                $leave->employee_id      = $request->employee_id;
                $leave->leave_type_id    = $request->leave_type_id;
                $leave->start_date       = $request->start_date;
                $leave->end_date         = $request->end_date;
                $leave->total_leave_days = 0;
                $leave->leave_reason     = $request->leave_reason;
                $leave->other_leave     = $request->other_leave;
                $leave->remark           = $request->remark;

                $leave->save();

                return redirect()->route('leave.index')->with('success', __('Leave successfully updated.'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy(Leave $leave)
    {
        if(\Auth::user()->can('delete leave'))
        {
            if($leave->created_by == \Auth::user()->creatorId())
            {
                $leave->delete();

                return redirect()->route('leave.index')->with('success', __('Leave successfully deleted.'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function action($id)
    {
        $leave     = Leave::find($id);
        $employee  = Employee::find($leave->employee_id);
        $leavetype = LeaveType::find($leave->leave_type_id);

        return view('leave.action', compact('employee', 'leavetype', 'leave'));
    }

    public function changeaction(Request $request)
    {
        $leave = Leave::find($request->leave_id);
        
        if (!$leave) {
            return redirect()->route('leave.index')->with('error', __('Leave not found.'));
        }
    
        $leave->status = $request->status;
    
        if ($leave->status == 'Approval') {
            $startDate = new \DateTime($leave->start_date);
            $endDate = new \DateTime($leave->end_date);
            $totalLeaveDays = $startDate->diff($endDate)->days + 1;
    
            $leave->total_leave_days = $totalLeaveDays;
            $leave->status = 'Approved';
    
            // Deduct leave from the correct employee's balance
            $employee = Employee::find($leave->employee_id);
            $leaveType = LeaveType::find($leave->leave_type_id);
    
            if (!$employee || !$leaveType) {
                return redirect()->route('leave.index')->with('error', __('Invalid leave type or employee.'));
            }
    
            $usedLeaves = Leave::where('employee_id', $employee->id)
                ->where('leave_type_id', $leave->leave_type_id)
                ->where('status', 'Approved')
                ->sum('total_leave_days');
    
            $remainingLeaves = $leaveType->days - $usedLeaves;
    
            if ($totalLeaveDays > $remainingLeaves) {
                return redirect()->route('leave.index')->with('error', __('Not enough leave balance.'));
            }
        }
    
        $leave->save();
    
        return redirect()->route('leave.index')->with('success', __('Leave status successfully updated.'));
    }
    


    public function jsoncount(Request $request)
    {

        // $leave_counts = LeaveType::select(\DB::raw('COALESCE(SUM(leaves.total_leave_days),0) AS total_leave, leave_types.title, leave_types.days,leave_types.id'))
        //                          ->leftjoin('leaves', function ($join) use ($request){
        //     $join->on('leaves.leave_type_id', '=', 'leave_types.id');
        //     $join->where('leaves.employee_id', '=', $request->employee_id);
        // }
        // )->groupBy('leaves.leave_type_id')->get();

        $leave_counts=[];
        $leave_types = LeaveType::where('created_by',\Auth::user()->creatorId())->get();
        foreach ($leave_types as  $type) {
            $counts=Leave::select(\DB::raw('COALESCE(SUM(leaves.total_leave_days),0) AS total_leave'))->where('leave_type_id',$type->id)->groupBy('leaves.leave_type_id')->where('employee_id',$request->employee_id)->first();

            $leave_count['total_leave']=!empty($counts)?$counts['total_leave']:0;
            $leave_count['title']=$type->title;
            $leave_count['days']=$type->days;
            $leave_count['id']=$type->id;
            $leave_counts[]=$leave_count;
        }


        return $leave_counts;

    }
}
