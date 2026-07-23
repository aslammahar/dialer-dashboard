<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;

use App\Models\Employee;
use App\Models\User;

use App\Models\Suspension;
use Illuminate\Http\Request;

class SuspensionController extends Controller
{
    /**
     * Display a listing of the resource.->can('manage termination'))
     */
    public function index()
    {
        if (\Auth::user()) {
            if (Auth::user()->type == 'employee') {
                $emp = Employee::where('user_id', '=', \Auth::user()->id)->first();
                $suspensions = Suspension::where('created_by', '=', \Auth::user()->creatorId())->where('employee_id', '=', $emp->id)->get();
            } else {
                $suspensions = Suspension::where('created_by', '=', \Auth::user()->creatorId())->get();
            }

            return view('suspension.index', compact('suspensions'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (\Auth::user()->can('create suspension')) {
            // $employees        = Employee::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $users = User::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');

            return view('suspension.create', compact('users'));
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        // Check if the user has permission to create a termination
        //   if (\Auth::user()->can('create termination')) {

        // Validate the request data
        $validator = \Validator::make(
            $request->all(),
            [
                'userid' => 'required',
                'suspended_by' => 'required',
                'start_date' => 'required',
                'end_date' => 'required',
                'reason' => 'required',

            ]
        );

        // If validation fails, redirect back with error message
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }

        // Create a new termination record
        $suspension = new Suspension();
        $suspension->userid = $request->userid;

        $suspension->suspended_by = $request->suspended_by;

        $suspension->start_date = $request->start_date;

        // $suspension->suspended_by = $request->suspended_by;

        $suspension->end_date = $request->end_date;
        // $suspension->newEloquentBuilder_date = $request->end_date;
        $suspension->reason = $request->reason;
        $suspension->created_by = \Auth::user()->creatorId();
        $suspension->save();



        // Redirect with success message
        return redirect()->route('suspension.index')->with('success', __('Suspension successfully created.'));
        // } else {
        //     // Redirect back with permission denied error
        //     return redirect()->back()->with('error', __('Permission denied.'));
        // }
    }

    /**
     * Display the specified resource.
     */
    public function show(Suspension $suspension)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Suspension $suspension)
    {
        if (\Auth::user()->can('update suspension')) {
            $users = User::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            if ($suspension->created_by == \Auth::user()->creatorId()) {

                return view('suspension.edit', compact('suspension', 'users'));
            } else {
                return response()->json(['error' => __('Permission denied.')], 401);
            }
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
        ;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Suspension $suspension)
    {
        if (\Auth::user()->can('edit suspension')) {
            if ($suspension->created_by == \Auth::user()->creatorId()) {
                $validator = \Validator::make(
                    $request->all(),
                    [
                        'userid' => 'required',
                        'suspended_by' => 'required',
                        'start_date' => 'required',
                        'end_date' => 'required',
                        'reason' => 'required',
                    ]
                );

                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }


                $suspension = new Suspension();
                $suspension->userid = $request->userid;

                $suspension->suspended_by = $request->suspended_by;

                $suspension->start_date = $request->start_date;

                // $suspension->suspended_by = $request->suspended_by;

                $suspension->end_date = $request->end_date;
                // $suspension->newEloquentBuilder_date = $request->end_date;
                $suspension->reason = $request->reason;
                $suspension->created_by = \Auth::user()->creatorId();
                $suspension->save();

                return redirect()->route('suspension.index')->with('success', __('Suspension successfully updated.'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Suspension $suspension)
    {
        if (\Auth::user()->can('delete suspension')) {
            if ($suspension->created_by == \Auth::user()->creatorId()) {
                $suspension->delete();

                return redirect()->route('suspension.index')->with('success', __('Suspension successfully deleted.'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
