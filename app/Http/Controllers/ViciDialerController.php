<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;

class ViciDialerController extends Controller
{
    public function showDialer()
    {
        $user = Auth::user();
        $dialerNo = $user->dialer_no;
        $vdUser = $user->dialer_id;
        $vdPass = $user->dialer_password;
        // $vdPass = 'hello123';
        $userType = $user->type;

        // Fetch the dialer weblink from dialerlist_tb table
        $dialerLink = DB::table('dialerlist_tb')
            ->where('dialer_no', $dialerNo)
            ->value('dialer_weblink');
        // dd($dialerLink);

        // Determine the campaign based on the dialer number and user type
        $campaignMap = [
            ['dialer_no' => 'Dialer 4', 'usertype' => 'Avatar', 'campaign_id' => '001'],
            ['dialer_no' => 'Dialer 4', 'usertype' => 'Voice', 'campaign_id' => '002'],
            ['dialer_no' => 'Dialer 2', 'usertype' => 'Avatar', 'campaign_id' => '1001'],
            ['dialer_no' => 'Dialer 1', 'usertype' => 'Avatar', 'campaign_id' => '004'],
        ];

        $vdCamp = collect($campaignMap)
            ->firstWhere(function ($item) use ($dialerNo, $userType) {
                return $item['dialer_no'] == $dialerNo && $item['usertype'] == $userType;
            })['campaign_id'] ?? null;
        //  dd($vdCamp);
        if (!$dialerLink || !$vdCamp) {
            return response()->json(['error' => 'Dialer configuration not found'], 404);
        }

        // Construct the ViciDialer URL
        $dialerUrl = "https://{$dialerLink}/agc/vicidial.php?phone_login={$vdUser}&phone_pass={$vdPass}&VD_login={$vdUser}&VD_pass={$vdPass}&VD_campaign={$vdCamp}";
        // dd($dialerUrl);
        return view('dialer.dialer_login', compact('dialerUrl'));
    }



    // dialer stats starts here



    public function getDialerStats(Request $request)
    {
        // Get unique filter values
        $listNames = DB::table('avatar_leads')->distinct()->pluck('list_name');
        $centerNames = DB::table('avatar_leads')->distinct()->pluck('centername');
        $dialerNames = DB::table('avatar_leads')->distinct()->pluck('dialername');
        $listIds = DB::table('avatar_leads')->distinct()->pluck('entry_list_id');

        // Build the query
        $query = DB::table('avatar_leads')
            ->select('entry_list_id', 'list_name', 'centername', 'dialername', DB::raw('COUNT(*) as count'))
            ->groupBy('entry_list_id', 'list_name', 'centername', 'dialername');

        // Apply filters
        if ($request->has('list_name') && $request->list_name != '') {
            $query->where('list_name', $request->input('list_name'));
        }
        if ($request->has('entry_list_id') && $request->entry_list_id != '') {
            $query->where('entry_list_id', $request->input('entry_list_id'));
        }

        if ($request->has('centername') && $request->centername != '') {
            $query->where('centername', $request->input('centername'));
        }

        if ($request->has('dialername') && $request->dialername != '') {
            $query->where('dialername', $request->input('dialername'));
        }

        // Apply date range filter
        if ($request->has('start_date') && $request->input('start_date') != '' && $request->has('end_date') && $request->input('end_date') != '') {
            $query->whereBetween('created_at', [
                Carbon::parse($request->input('start_date'))->startOfDay(),
                Carbon::parse($request->input('end_date'))->endOfDay()
            ]);
        }

        $entryListIds = $query->get();

        return view('dialer.dialer_stats', compact('entryListIds', 'listNames', 'centerNames', 'dialerNames', 'listIds'));
    }

    // dialer stats ends heree


}
