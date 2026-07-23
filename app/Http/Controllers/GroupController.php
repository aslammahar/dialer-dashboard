<?php

namespace App\Http\Controllers;


use App\Models\Group;
use App\Models\Pipeline;
use Illuminate\Http\Request;

class GroupController extends Controller
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
     * Display a listing of the regroup.     
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(\Auth::user()->can('manage group'))
        {
            $groups   = Group::select('groups.*', 'pipelines.name as pipeline')->join('pipelines', 'pipelines.id', '=', 'groups.pipeline_id')->where('pipelines.created_by', '=', \Auth::user()->ownerId())->where('groups.created_by', '=', \Auth::user()->ownerId())->orderBy('groups.pipeline_id')->get();
            $group = Group::where('created_by',\Auth::user()->ownerId())->get();
            $pipelines = [];

            foreach($groups as $group)
            {
                if(!array_key_exists($group->pipeline_id, $pipelines))
                {
                    $pipelines[$group->pipeline_id]           = [];
                    $pipelines[$group->pipeline_id]['name']   = $group['pipeline'];
                    $pipelines[$group->pipeline_id]['groups'] = [];
                }
                $pipelines[$group->pipeline_id]['groups'][] = $group;
            }

            return view('groups.index')->with('pipelines', $pipelines);
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Show the form for creating a new regroup.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(\Auth::user()->can('create group'))
        {
            $pipelines = Pipeline::where('created_by', '=', \Auth::user()->ownerId())->get()->pluck('name', 'id');
            $colors = Group::$colors;

            return view('groups.create')->with('pipelines', $pipelines)->with('colors', $colors);
        }
        else
        {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

    /**
     * Store a newly created regroup in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(\Auth::user()->can('create group'))
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

                return redirect()->route('groups.index')->with('error', $messages->first());
            }

            $group              = new Group();
            $group->name        = $request->name;
            $group->color       = $request->color;
            $group->pipeline_id = $request->pipeline_id;
            $group->created_by  = \Auth::user()->ownerId();
            $group->save();

            return redirect()->route('groups.index')->with('success', __('Group successfully created!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Display the specified regroup.
     *
     * @param \App\Group $group
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Group $group)
    {
        return redirect()->route('groups.index');
    }

    /**
     * Show the form for editing the specified regroup.
     *
     * @param \App\Group $group
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Group $group)
    {
        if(\Auth::user()->can('edit group'))
        {
            if($group->created_by == \Auth::user()->ownerId())
            {
                $pipelines = Pipeline::where('created_by', '=', \Auth::user()->ownerId())->get()->pluck('name', 'id');
                $colors    = Group::$colors;

                return view('groups.edit', compact('group', 'pipelines', 'colors'));
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
     * Update the specified regroup in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Group $group
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Group $group)
    {
        if(\Auth::user()->can('edit group'))
        {

            if($group->created_by == \Auth::user()->ownerId())
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

                $group->name        = $request->name;
                $group->color       = $request->color;
                $group->pipeline_id = $request->pipeline_id;
                $group->save();

                return redirect()->route('groups.index')->with('success', __('Group successfully updated!'));
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
     * Remove the specified regroup from storage.
     *
     * @param \App\Group $group
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Group $group)
    {
        if(\Auth::user()->can('delete group'))
        {
            if($group->created_by == \Auth::user()->ownerId())
            {
                $group->delete();

                return redirect()->route('groups.index')->with('success', __('Group successfully deleted!'));
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
