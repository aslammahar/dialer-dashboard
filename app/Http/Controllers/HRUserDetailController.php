<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserDetail;
use App\Models\UserBankDetail;
use Illuminate\Http\Request;

class HRUserDetailController extends Controller
{
    public function index()
    {
        // Only get users who have user details
        $users = User::whereHas('userDetail')
                    ->with(['userDetail', 'bankDetails'])
                    ->paginate(20);
        
        return view('hr.user-details.index', compact('users'));
    }

    public function show($userId)
    {
        $user = User::with([
            'userDetail', 
            'bankDetails',
            'userDetail.cnicFront',
            'userDetail.cnicBack'
        ])->findOrFail($userId);
        
        return view('hr.user-details.show', compact('user'));
    }

    public function updateBankStatus(Request $request, $bankId)
    {
        $request->validate([
            'status' => 'required|in:verified,rejected,unverified'
        ]);

        $bankDetail = UserBankDetail::findOrFail($bankId);
        $bankDetail->status = $request->status;
        $bankDetail->save();

        return response()->json(['success' => 'Bank status updated successfully!']);
    }
}