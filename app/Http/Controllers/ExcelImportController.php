<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Imports\QaLeadsVoiceImport;
use App\Imports\AvatarLeadsImport;
use App\Models\QaLeadsVoice;
use App\Models\AvatarLead;
use App\Models\AvatarQALead;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;




class ExcelImportController extends Controller
{

    // authentica the user for only QA type


    public function index()
    {
        if (Auth::user()->type == 'QA' || Auth::user()->type == 'QA Manager' || Auth::user()->type == 'Director') {
            // ⚡ PERFORMANCE: Optimized query with select only needed columns and pagination
            $avatarLeads = AvatarLead::where('QapersonId', Auth::user()->id)
                ->select([
                    'id',
                    'lead_id',
                    'dialer_id',
                    'recording_link',
                    'updated_at',
                    'QAstatus',
                    'Qacomments'
                ])
                ->orderBy('updated_at', 'desc')
                ->paginate(50); // Use pagination instead of take(200) for better performance

            // Pass the data to the view
            return view('qa-section.index', compact('avatarLeads'));
        } else {
            return redirect()->back()->with('error', 'Permission Denied.');
        }
    }




    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        $file = $request->file('file');

        // Directly import the data using the QaLeadsVoiceImport class
        Excel::import(new QaLeadsVoiceImport, $file);

        return redirect()->back()->with('success', 'Data imported successfully.');
    }


    public function avatarimport(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);
        $file = $request->file('file');
        Excel::import(new AvatarLeadsImport, $file);
        return redirect()->back()->with('success', 'Data imported successfully');
    }












    public function showUserLeads()
    {
        if (Auth::check()) {
            $userEmail = Auth::user()->email;
            $userLeads = QaLeadsVoice::where('user_email', $userEmail)->get();
            return view('voiceqa.imported-leads', ['userLeads' => $userLeads]);
        } else {
            // Redirect to login page or show an appropriate message
            return redirect()->route('login')->with('error', 'Please log in to view your leads.');
        }
    }
}
