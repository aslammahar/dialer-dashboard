<?php

namespace App\Http\Controllers;

use App\Models\Center;
use Illuminate\Http\Request;

class CenterController extends Controller
{
    public function index()
    {
        if (!\Auth::user()->can('manage center')) {
            return redirect()->back()->with('error', __('You do not have permission to access centers.'));
        }

        $centers = Center::with('creator')->orderBy('created_at', 'desc')->get();
        return view('center.index', compact('centers'));
    }

    public function create()
    {
        if (!\Auth::user()->can('manage center')) {
            return redirect()->back()->with('error', __('You do not have permission.'));
        }

        return view('center.create');
    }

    public function store(Request $request)
    {
        if (!\Auth::user()->can('manage center')) {
            return redirect()->back()->with('error', __('You do not have permission.'));
        }

        $request->validate([
            'center_name' => 'required|max:255',
            'description' => 'nullable',
        ]);

        Center::create([
            'center_name' => $request->center_name,
            'description' => $request->description,
            'created_by'  => \Auth::id(),
        ]);

        return redirect()->route('centers.index')->with('success', __('Center successfully created.'));
    }

    public function edit($id)
    {
        if (!\Auth::user()->can('manage center')) {
            return redirect()->back()->with('error', __('You do not have permission.'));
        }

        $center = Center::findOrFail($id);
        return view('center.edit', compact('center'));
    }

    public function update(Request $request, $id)
    {
        if (!\Auth::user()->can('manage center')) {
            return redirect()->back()->with('error', __('You do not have permission.'));
        }

        $request->validate([
            'center_name' => 'required|max:255',
            'description' => 'nullable',
        ]);

        $center = Center::findOrFail($id);
        $center->update([
            'center_name' => $request->center_name,
            'description' => $request->description,
        ]);

        return redirect()->route('centers.index')->with('success', __('Center successfully updated.'));
    }

    public function destroy($id)
    {
        if (!\Auth::user()->can('manage center')) {
            return redirect()->back()->with('error', __('You do not have permission.'));
        }

        $center = Center::findOrFail($id);
        $center->delete();

        return redirect()->route('centers.index')->with('success', __('Center successfully deleted.'));
    }
}