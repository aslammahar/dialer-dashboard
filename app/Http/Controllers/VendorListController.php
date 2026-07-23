<?php
// app/Http/Controllers/VendorListController.php

namespace App\Http\Controllers;

use App\Models\VendorList;
use Illuminate\Http\Request;

class VendorListController extends Controller
{
    /**
     * Display the vendor lists
     */
    public function index()
    {
        // Generate/update vendor lists from closed_calls
        VendorList::generateVendorLists();
        
        $vendorLists = VendorList::orderBy('list_id')->get();
        
        return view('vendor-lists.index', compact('vendorLists'));
    }

    /**
     * Update vendor list record
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'vendor_name' => 'nullable|string|max:255',
            'file_name' => 'nullable|string|max:255',
            'total_numbers' => 'nullable|integer|min:0',
            'dnc' => 'nullable|integer|min:0',
            'duplicate' => 'nullable|integer|min:0',
            'clean' => 'nullable|integer|min:0',
        ]);

        $vendorList = VendorList::findOrFail($id);
        
        // Update editable fields
        $vendorList->update([
            'vendor_name' => $request->vendor_name,
            'file_name' => $request->file_name,
            'total_numbers' => $request->total_numbers ?? 0,
            'dnc' => $request->dnc ?? 0,
            'duplicate' => $request->duplicate ?? 0,
            'clean' => $request->clean ?? 0,
        ]);

        // Recalculate conversions
        $vendorList->updateConversions();

        return response()->json([
            'success' => true,
            'message' => 'Vendor list updated successfully',
            'data' => $vendorList->fresh()
        ]);
    }

    /**
     * Get single vendor list for editing
     */
    public function show($id)
    {
        $vendorList = VendorList::findOrFail($id);
        return response()->json($vendorList);
    }

    /**
     * Refresh data from closed_calls table
     */
    public function refresh()
    {
        VendorList::generateVendorLists();
        
        return response()->json([
            'success' => true,
            'message' => 'Vendor lists refreshed successfully'
        ]);
    }

    
}