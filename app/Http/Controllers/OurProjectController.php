<?php

namespace App\Http\Controllers;

use App\Models\OurProject;
use Illuminate\Http\Request;

class OurProjectController extends Controller
{
    public function index()
    {
        $projects = OurProject::all();
        return view('our_projects.index', compact('projects'));
    }

    public function create()
    {
        return view('our_projects.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        OurProject::create($request->all());

        return redirect()->route('our_projects.index')->with('success', 'Project created successfully.');
    }

    public function show(OurProject $our_project)
    {
        return view('our_projects.show', compact('our_project'));
    }

    public function edit(OurProject $our_project)
    {
        return view('our_projects.edit', compact('our_project'));
    }

    public function update(Request $request, OurProject $our_project)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $our_project->update($request->all());

        return redirect()->route('our_projects.index')->with('success', 'Project updated successfully.');
    }

    public function destroy(OurProject $our_project)
    {
        $our_project->delete();

        return redirect()->route('our_projects.index')->with('success', 'Project deleted successfully.');
    }
}