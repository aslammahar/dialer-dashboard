<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataVendor;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class DataVendorController extends Controller
{
    /**
     * Create a new data vendor
     */
    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // log the request

        $dataVendor = DataVendor::create([
            'vendor_name' => $request->name,
        ]);

       // return view
        return redirect()->back()->with('success', __('Data Vendor successfully created.'));
    }

    /**
     * List all data vendors
     */
    public function index(){
        $dataVendors = DataVendor::all();
        return view('data-vendors.index')->with('dataVendors', $dataVendors);
    }

    /**
     * Get All User Where type = vendor
     */
    // public function getVendorUsers()
    // {
    //     $vendors = User::where('type', 'vendor')->get();
    //     return view('data-vendors.assign-users')->with('vendors', $vendors);
    // }
    public function getVendorUsers($vendorId)
    {
        $vendor = DataVendor::findOrFail($vendorId);
        $users = User::whereNull('vendor_id')
        ->where('type', 'vendor') // only users with type vendor
        ->get(); // only users without vendor

        return view('data-vendors.assign-users', compact('vendor', 'users'));
    }

    public function assignUsers(Request $request, $vendorId)
    {
        $vendor = DataVendor::findOrFail($vendorId);

        // Update selected users with this vendor_id
        User::whereIn('id', $request->user_ids)->update(['vendor_id' => $vendor->id]);

        return redirect()->route('data-vendor.index')->with('success', 'Users assigned successfully!');
    }

    /**
     * Get Data vendor reports
     */

    public function getDataVendorReports($vendorId)
    {
        $vendor = DataVendor::findOrFail($vendorId);
        $vendor->load('numberLists'); // Eager load number lists
        $reports = $vendor->numberLists; // Get all number lists for this vendor

        return view('data-vendors.reports', compact('vendor', 'reports'));
    }

    /**
     * Data Report for Auth Vendor user
     */
   
    public function getAuthVendorUserReport()
    {
        $vendor = auth()->user()->vendor;

        if (!$vendor) {
            abort(404);
        }

        $vendor->load('numberLists');
        $reports = $vendor->numberLists;

        return view('data-vendors.vendor_specific_reports', compact('vendor', 'reports'));
    }
}
