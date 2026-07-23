<?php

namespace App\Http\Controllers;

use App\Models\Validator;
use Illuminate\Http\Request;

class ValidatorController extends Controller
{
    public function index()
    {
        $validators = Validator::paginate(10);
        return view('validators.index', compact('validators'));
    }

    public function create()
    {
        return view('validators.create');
    }

    public function store(Request $request)
    {
        $request->validate(Validator::$rules);
        Validator::create($request->all());
        return redirect()->route('validators.index')->with('success', 'Validator created successfully.');
    }

    public function show(Validator $validator)
    {
        return view('validators.show', compact('validator'));
    }

    public function edit(Validator $validator)
    {
        return view('validators.edit', compact('validator'));
    }

    public function update(Request $request, Validator $validator)
    {
        $request->validate(Validator::$rules);
        $validator->update($request->all());
        return redirect()->route('validators.index')->with('success', 'Validator updated successfully.');
    }

    public function destroy(Validator $validator)
    {
        $validator->delete();
        return redirect()->route('validators.index')->with('success', 'Validator deleted successfully.');
    }
}
