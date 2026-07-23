<?php

namespace App\Http\Controllers;

use App\Models\TaxSlab;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TaxSlabController extends Controller
{
    public function index()
    {
        $taxSlabs = TaxSlab::orderBy('min_salary')->paginate(15);
        return view('tax-slabs.index', compact('taxSlabs'));
    }

    public function create()
    {
        return view('tax-slabs.create');
    }

   public function store(Request $request)
{
    $validated = $request->validate([
        'min_salary' => 'required|numeric|min:0',
        'max_salary' => 'nullable|numeric|gt:min_salary',
        'fixed_amount' => 'nullable|numeric|min:0',
        'tax_percentage' => 'required|numeric|min:0|max:100',
        'description' => 'nullable|string|max:500',
        'is_active' => 'boolean'
    ]);

    // Set default values
    $validated['fixed_amount'] = $validated['fixed_amount'] ?? 0;
    $validated['is_active'] = $request->has('is_active') ? 1 : 0;

    // Check for overlapping slabs
    $overlapping = TaxSlab::where('is_active', true)
        ->where(function($query) use ($validated) {
            $query->where(function($q) use ($validated) {
                $q->where('min_salary', '<=', $validated['min_salary'])
                  ->where(function($q2) use ($validated) {
                      $q2->whereNull('max_salary')
                         ->orWhere('max_salary', '>=', $validated['min_salary']);
                  });
            })->orWhere(function($q) use ($validated) {
                if (isset($validated['max_salary'])) {
                    $q->where('min_salary', '<=', $validated['max_salary'])
                      ->where(function($q2) use ($validated) {
                          $q2->whereNull('max_salary')
                             ->orWhere('max_salary', '>=', $validated['max_salary']);
                      });
                }
            });
        })
        ->exists();

    if ($overlapping) {
        return back()
            ->withErrors(['min_salary' => 'This salary range overlaps with an existing active tax slab.'])
            ->withInput();
    }

    try {
        TaxSlab::create($validated);

        return redirect()->route('tax-slabs.index')
            ->with('success', 'Tax slab created successfully');
    } catch (\Exception $e) {
        \Log::error('Tax slab creation error: ' . $e->getMessage());
        return back()
            ->with('error', 'Failed to create tax slab: ' . $e->getMessage())
            ->withInput();
    }
}

    public function show(TaxSlab $taxSlab)
    {
        return view('tax-slabs.show', compact('taxSlab'));
    }

    public function edit(TaxSlab $taxSlab)
    {
        return view('tax-slabs.edit', compact('taxSlab'));
    }

    public function update(Request $request, TaxSlab $taxSlab)
{
    $validated = $request->validate([
        'min_salary' => 'required|numeric|min:0',
        'max_salary' => 'nullable|numeric|gt:min_salary',
        'fixed_amount' => 'nullable|numeric|min:0',
        'tax_percentage' => 'required|numeric|min:0|max:100',
        'description' => 'nullable|string|max:500',
        'is_active' => 'boolean'
    ]);

    // Handle fixed_amount - set to 0 if not provided
    $validated['fixed_amount'] = $validated['fixed_amount'] ?? 0;
    
    // Handle is_active checkbox - IMPORTANT FIX
    $validated['is_active'] = $request->has('is_active') ? 1 : 0;

    // Check for overlapping slabs (excluding current one)
    $overlapping = TaxSlab::where('id', '!=', $taxSlab->id)
        ->where('is_active', true)
        ->where(function($query) use ($validated) {
            $query->where(function($q) use ($validated) {
                $q->where('min_salary', '<=', $validated['min_salary'])
                  ->where(function($q2) use ($validated) {
                      $q2->whereNull('max_salary')
                         ->orWhere('max_salary', '>=', $validated['min_salary']);
                  });
            })->orWhere(function($q) use ($validated) {
                if (isset($validated['max_salary'])) {
                    $q->where('min_salary', '<=', $validated['max_salary'])
                      ->where(function($q2) use ($validated) {
                          $q2->whereNull('max_salary')
                             ->orWhere('max_salary', '>=', $validated['max_salary']);
                      });
                }
            });
        })
        ->exists();

    if ($overlapping) {
        return back()
            ->withErrors(['min_salary' => 'This salary range overlaps with an existing active tax slab.'])
            ->withInput();
    }

    try {
        $taxSlab->update($validated);

        return redirect()->route('tax-slabs.index')
            ->with('success', 'Tax slab updated successfully');
    } catch (\Exception $e) {
        \Log::error('Tax slab update error: ' . $e->getMessage());
        return back()
            ->with('error', 'Failed to update tax slab: ' . $e->getMessage())
            ->withInput();
    }
}

    public function destroy(TaxSlab $taxSlab)
    {
        if ($taxSlab->monthlySalaries()->exists()) {
            return back()->with('error', 'Cannot delete tax slab that is being used in salary records');
        }

        $taxSlab->delete();

        return redirect()->route('tax-slabs.index')
            ->with('success', 'Tax slab deleted successfully');
    }

    public function toggleStatus(TaxSlab $taxSlab)
    {
        $taxSlab->update(['is_active' => !$taxSlab->is_active]);

        return back()->with('success', 'Tax slab status updated successfully');
    }

    /**
     * Calculate tax for preview (AJAX)
     */
    public function calculatePreview(Request $request)
    {
        try {
            $monthlySalary = $request->input('monthly_salary');
            
            if (!$monthlySalary || $monthlySalary <= 0) {
                return response()->json(['error' => 'Invalid salary amount'], 400);
            }

            $breakdown = TaxSlab::getTaxBreakdown($monthlySalary);

            if (!$breakdown) {
                return response()->json(['error' => 'No applicable tax slab found'], 404);
            }

            return response()->json($breakdown);
        } catch (\Exception $e) {
            Log::error('Tax calculation error: ' . $e->getMessage());
            return response()->json(['error' => 'Error calculating tax'], 500);
        }
    }
}