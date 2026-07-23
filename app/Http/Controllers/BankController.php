<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BankController extends Controller
{
    public function index()
    {
        $banks = Bank::ordered()->paginate(20);
        $categories = Bank::select('category')
                          ->distinct()
                          ->whereNotNull('category')
                          ->orderBy('category')
                          ->pluck('category');
        
        return view('banks.index', compact('banks', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:banks,name',
            'code' => 'required|string|max:50|unique:banks,code',
            'category' => 'nullable|string|max:100',
            'is_active' => 'boolean'
        ]);

        try {
            $bank = Bank::create([
                'name' => $validated['name'],
                'code' => $validated['code'],
                'category' => $validated['category'] ?? null,
                'is_active' => $request->has('is_active')
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Bank added successfully',
                    'bank' => $bank
                ]);
            }

            return redirect()->route('banks.index')
                           ->with('success', 'Bank added successfully');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to add bank: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Failed to add bank: ' . $e->getMessage())
                        ->withInput();
        }
    }

    public function update(Request $request, Bank $bank)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:banks,name,' . $bank->id,
            'code' => 'required|string|max:50|unique:banks,code,' . $bank->id,
            'category' => 'nullable|string|max:100',
            'is_active' => 'boolean'
        ]);

        try {
            $bank->update([
                'name' => $validated['name'],
                'code' => $validated['code'],
                'category' => $validated['category'] ?? null,
                'is_active' => $request->has('is_active')
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Bank updated successfully',
                    'bank' => $bank
                ]);
            }

            return redirect()->route('banks.index')
                           ->with('success', 'Bank updated successfully');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update bank: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Failed to update bank: ' . $e->getMessage())
                        ->withInput();
        }
    }

    public function destroy(Bank $bank)
    {
        try {
            // Check if bank is being used in user_bank_details
            $isUsed = DB::table('user_bank_details')
                       ->where('bank_name', $bank->name)
                       ->exists();

            if ($isUsed) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete bank. It is being used by users.'
                ], 400);
            }

            $bank->delete();

            return response()->json([
                'success' => true,
                'message' => 'Bank deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete bank: ' . $e->getMessage()
            ], 500);
        }
    }

    public function toggleStatus(Bank $bank)
    {
        try {
            $bank->is_active = !$bank->is_active;
            $bank->save();

            return response()->json([
                'success' => true,
                'message' => 'Bank status updated successfully',
                'is_active' => $bank->is_active
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status: ' . $e->getMessage()
            ], 500);
        }
    }

    // API endpoint to get active banks for user forms
    public function getActiveBanks()
    {
        $banks = Bank::active()->ordered()->get();
        
        return response()->json($banks);
    }
}