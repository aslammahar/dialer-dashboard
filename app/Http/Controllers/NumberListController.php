<?php
// app/Http/Controllers/NumberListController.php

namespace App\Http\Controllers;

use App\Models\NumberList;
use App\Models\DataVendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NumberListController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $numberLists = NumberList::paginate(15);

        // Add sales data for each record
        foreach ($numberLists as $list) {
            $list->sales_count = $list->sales;
            $list->conversion_display = $list->conversion_rate;
        }

        return view('number_lists.index', compact('numberLists'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $vendors = DataVendor::all(); // fetch all vendors
        return view('number_lists.create', compact('vendors'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'data_vendor' => 'required|string|max:255',
            'file_name' => 'required|string|max:255',
            'list_ids' => 'required|string', // Comma-separated string from tags input
            'total_numbers' => 'required|integer|min:0',
            'blocks_dubs_from_same_file' => 'nullable|integer|min:0',
            'dialer_scrubbing' => 'nullable|integer|min:0',
            'dnc_clean_numbers' => 'nullable|integer|min:0',
            'clean' => 'nullable|integer|min:0',
            'vendor_id' => 'required|integer|exists:data_vendors,id'
        ]);

        // Convert comma-separated list IDs into array
        $listIds = array_map('trim', explode(',', $request->list_ids));

        // Prepare data for bulk insert
        $dataToInsert = [];
        foreach ($listIds as $listId) {
            // Optional: skip empty values
            if (!empty($listId)) {
                $dataToInsert[] = [
                    'date' => $request->date,
                    'vendor_id' => $request->vendor_id,
                    'data_vendor' => $request->data_vendor,
                    'file_name' => $request->file_name,
                    'list_id' => $listId,
                    'total_numbers' => $request->total_numbers,
                    'blocks_dubs_from_same_file' => $request->blocks_dubs_from_same_file ?? 0,
                    'dialer_scrubbing' => $request->dialer_scrubbing ?? 0,
                    'dnc_clean_numbers' => $request->dnc_clean_numbers ?? 0,
                    'clean' => $request->clean ?? 0,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
        }

        // Insert all rows at once
        NumberList::insert($dataToInsert);

        return redirect()->route('number-lists.index')
            ->with('success', 'Number lists created successfully.');
    }


    /**
     * Display the specified resource.
     */
    public function show(NumberList $numberList)
    {
        $numberList->sales_count = $numberList->sales;
        $numberList->conversion_display = $numberList->conversion_rate;

        return view('number_lists.show', compact('numberList'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(NumberList $numberList)
    {
        $vendors = DataVendor::all(); // fetch all vendors
        return view('number_lists.edit', compact('numberList', 'vendors'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, NumberList $numberList)
    {
        $request->validate([
            'date' => 'required|date',
            'data_vendor' => 'required|string|max:255',
            'file_name' => 'required|string|max:255',
            'list_id' => 'required|integer|unique:number_lists,list_id,' . $numberList->id,
            'total_numbers' => 'required|integer|min:0',
            'blocks_dubs_from_same_file' => 'nullable|integer|min:0',
            'dialer_scrubbing' => 'nullable|integer|min:0',
            'dnc_clean_numbers' => 'nullable|integer|min:0',
            'clean' => 'nullable|integer|min:0',
            'vendor_id' => 'required|integer|exists:data_vendors,id'
        ]);

        $numberList->update($request->all());

        return redirect()->route('number-lists.index')
            ->with('success', 'Number list updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(NumberList $numberList)
    {
        $numberList->delete();

        return redirect()->route('number-lists.index')
            ->with('success', 'Number list deleted successfully.');
    }
}
