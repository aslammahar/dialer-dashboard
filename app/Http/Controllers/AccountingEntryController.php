<?php

namespace App\Http\Controllers;

use App\Models\AccountingEntry;
use App\Models\User;
use Illuminate\Http\Request;

class AccountingEntryController extends Controller
{
    public function index()
    {
        $users = User::orderBy('name', 'asc')->get();
        $entries = AccountingEntry::with('user')->paginate(10);
        return view('accounting.index', compact('users', 'entries'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'description' => 'required|string',
            'accountant_title' => 'required|string',
        ]);

        AccountingEntry::create($request->all());

        return redirect()->route('accounting.index')->with('success', 'Entry added successfully.');
    }

    public function edit(AccountingEntry $entry)
    {
        $users = User::all();
        return view('accounting.edit', compact('entry', 'users'));
    }

    public function update(Request $request, AccountingEntry $entry)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'description' => 'required|string',
            'accountant_title' => 'required|string',
        ]);

        $entry->update($request->all());

        return redirect()->route('accounting.index')->with('success', 'Entry updated successfully.');
    }

    public function destroy(AccountingEntry $entry)
    {
        $entry->delete();
        return redirect()->route('accounting.index')->with('success', 'Entry deleted successfully.');
    }
}