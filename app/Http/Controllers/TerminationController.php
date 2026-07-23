<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Termination;
use App\Models\User;

use App\Models\TerminationType;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class TerminationController extends Controller
{
    public function index()
    {
        if(\Auth::user()->can('manage termination'))
        {
            if(Auth::user()->type == 'employee')
            {
                $emp          = Employee::where('user_id', '=', \Auth::user()->id)->first();
                $terminations = Termination::where('created_by', '=', \Auth::user()->creatorId())->where('employee_id', '=', $emp->id)->get();
            }
            else
            {
                $terminations = Termination::where('created_by', '=', \Auth::user()->creatorId())->get();
            }

            return view('termination.index', compact('terminations'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        if(\Auth::user()->can('create termination'))
        {
            $employees        = Employee::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $terminationtypes = TerminationType::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');

            return view('termination.create', compact('employees', 'terminationtypes'));
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function store(Request $request)

    {
  
        // Check if the user has permission to create a termination
        if (\Auth::user()->can('create termination')) {
    
            // Validate the request data
            $validator = \Validator::make(
                $request->all(), [
                    'employee_id' => 'required',
                    'termination_type' => 'required',
                    'notice_date' => 'required',
                    'termination_date' => 'required',
                ]
            );
    
            // If validation fails, redirect back with error message
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }
    
            // Create a new termination record
            $termination = new Termination();
            $termination->employee_id = $request->employee_id;
                        
            $termination->termination_type = $request->termination_type;
            $termination->notice_date = $request->notice_date;
            $termination->termination_date = $request->termination_date;
            $termination->description = $request->description;
            $termination->created_by = \Auth::user()->creatorId();
            $termination->save();
    
            // Get the settings
            $settings = Utility::settings();
    
            // Check if termination emails should be sent
            if ($settings['termination_sent'] == 1) {
                $employee = Employee::find($termination->employee_id);
                $termination->type = TerminationType::find($termination->termination_type);
    
                $terminationArr = [
                    'termination_name' => $employee->name,
                    'termination_email' => $employee->email,
                    'notice_date' => $termination->notice_date,
                    'termination_date' => $termination->termination_date,
                    'termination_type' => $request->termination_type,
                ];
    
                $resp = Utility::sendEmailTemplate('termination_sent', [$employee->id => $employee->email], $terminationArr);
                $employee = Employee::find($termination->employee_id);


                
                $userid = $employee->user_id;
             

                $user = User::where('id', $userid)->first();
                
                if ($user) {
                    $user->is_active = 0;
                    $user->save();
                }
    
                // Redirect with success message and optional email error
                return redirect()->route('termination.index')->with('success', __('Termination successfully created.') . (($resp['is_success'] == false && !empty($resp['error'])) ? '<br> <span class="text-danger">' . $resp['error'] . '</span>' : ''));
            }
    
            // Update user status to inactive
            
    
            // Redirect with success message
            return redirect()->route('termination.index')->with('success', __('Termination successfully created.'));
        } else {
            // Redirect back with permission denied error
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
    

    public function show(Termination $termination)
    {
        return redirect()->route('termination.index');
    }

    public function edit(Termination $termination)
    {
        if(\Auth::user()->can('edit termination'))
        {
            $employees        = Employee::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $terminationtypes = TerminationType::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            if($termination->created_by == \Auth::user()->creatorId())
            {

                return view('termination.edit', compact('termination', 'employees', 'terminationtypes'));
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

    public function update(Request $request, Termination $termination)
    {
        if(\Auth::user()->can('edit termination'))
        {
            if($termination->created_by == \Auth::user()->creatorId())
            {
                $validator = \Validator::make(
                    $request->all(), [
                                       'employee_id' => 'required',
                                       'termination_type' => 'required',
                                       'notice_date' => 'required',
                                       'termination_date' => 'required',
                                   ]
                );

                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }


                $termination->employee_id      = $request->employee_id;
                $termination->termination_type = $request->termination_type;
                $termination->notice_date      = $request->notice_date;
                $termination->termination_date = $request->termination_date;
                $termination->description      = $request->description;
                $termination->save();

                return redirect()->route('termination.index')->with('success', __('Termination successfully updated.'));
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

    public function destroy(Termination $termination)
    {
        if(\Auth::user()->can('delete termination'))
        {
            if($termination->created_by == \Auth::user()->creatorId())
            {
                $termination->delete();

                return redirect()->route('termination.index')->with('success', __('Termination successfully deleted.'));
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

    public function description($id)
    {
        $termination = Termination::find($id);

        return view('termination.description', compact('termination'));
    }

}
