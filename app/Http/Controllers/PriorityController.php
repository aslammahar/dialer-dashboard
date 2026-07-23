<?php

namespace App\Http\Controllers;

use App\Models\Priority;
use App\Models\Pipeline;
use Illuminate\Http\Request;

class PriorityController extends Controller     
{
     public function __construct()
    {
        $this->middleware(    
            [
                'auth',
                'XSS',
            ]
        );
    }

    /**
     * Display a listing of the repriority.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(\Auth::user()->can('manage priority'))
        {
            $priorities   = Priority::select('priorities.*', 'pipelines.name as pipeline')->join('pipelines', 'pipelines.id', '=', 'priorities.pipeline_id')->where('pipelines.created_by', '=', \Auth::user()->ownerId())->where('priorities.created_by', '=', \Auth::user()->ownerId())->orderBy('priorities.pipeline_id')->get();
            $priority = Priority::where('created_by',\Auth::user()->ownerId())->get();
            $pipelines = [];

            foreach($priorities as $priority)
            {
                if(!array_key_exists($priority->pipeline_id, $pipelines))
                {
                    $pipelines[$priority->pipeline_id]           = [];
                    $pipelines[$priority->pipeline_id]['name']   = $priority['pipeline'];
                    $pipelines[$priority->pipeline_id]['priorities'] = [];
                }
                $pipelines[$priority->pipeline_id]['priorities'][] = $priority;
            }

            return view('priorities.index')->with('pipelines', $pipelines);
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Show the form for creating a new repriority.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(\Auth::user()->can('create priority'))
        {
            $pipelines = Pipeline::where('created_by', '=', \Auth::user()->ownerId())->get()->pluck('name', 'id');
            $colors = Priority::$colors;

            return view('priorities.create')->with('pipelines', $pipelines)->with('colors', $colors);
        }
        else
        {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

    /**
     * Store a newly created repriority in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(\Auth::user()->can('create priority'))
        {

            $validator = \Validator::make(
                $request->all(), [
                                   'name' => 'required|max:20',
                                   'pipeline_id' => 'required',
                                   'color' => 'required',
                               ]
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->route('priorities.index')->with('error', $messages->first());
            }

            $priority              = new Priority();
            $priority->name        = $request->name;
            $priority->color       = $request->color;
            $priority->pipeline_id = $request->pipeline_id;
            $priority->created_by  = \Auth::user()->ownerId();
            $priority->save();

            return redirect()->route('priorities.index')->with('success', __('Priority successfully created!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Display the specified repriority.
     *
     * @param \App\Priority $priority
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Priority $priority)
    {
        return redirect()->route('priorities.index');
    }

    /**
     * Show the form for editing the specified repriority.
     *
     * @param \App\Priority $priority
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Priority $priority)
    {
        if(\Auth::user()->can('edit priority'))
        {
            if($priority->created_by == \Auth::user()->ownerId())
            {
                $pipelines = Pipeline::where('created_by', '=', \Auth::user()->ownerId())->get()->pluck('name', 'id');
                $colors    = Priority::$colors;

                return view('priorities.edit', compact('priority', 'pipelines', 'colors'));
            }
            else
            {
                return response()->json(['error' => __('Permission Denied.')], 401);
            }
        }
        else
        {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

    /**
     * Update the specified repriority in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Priority $priority
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Priority $priority)
    {
        if(\Auth::user()->can('edit priority'))
        {

            if($priority->created_by == \Auth::user()->ownerId())
            {

                $validator = \Validator::make(
                    $request->all(), [
                                       'name' => 'required|max:20',
                                       'pipeline_id' => 'required',
                                       'color' => 'required',
                                   ]
                );

                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->route('users')->with('error', $messages->first());
                }

                $priority->name        = $request->name;
                $priority->color       = $request->color;
                $priority->pipeline_id = $request->pipeline_id;
                $priority->save();

                return redirect()->route('priorities.index')->with('success', __('Priority successfully updated!'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission Denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Remove the specified repriority from storage.
     *
     * @param \App\Priority $priority
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Priority $priority)
    {
        if(\Auth::user()->can('delete priority'))
        {
            if($priority->created_by == \Auth::user()->ownerId())
            {
                $priority->delete();

                return redirect()->route('priorities.index')->with('success', __('Priority successfully deleted!'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission Denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }
}
