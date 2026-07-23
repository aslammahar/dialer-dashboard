<?php

namespace App\Http\Controllers;

use App\Models\AvatarLead;
use App\Models\Recording;
use App\Models\User;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class QaController extends Controller
{
    public function create()
    {
        $users = User::where('type', 'avatar')->get();
        return view('avatarqaleads', compact('users'));
    }

    public function store(Request $request) {}

    public function myAvatarLeads()
    {
        $avatarLeads = AvatarLead::where('user_id', auth()->user()->id)->get();
        return view('myavatarleads', compact('avatarLeads'));
    }

    // ─────────────────────────────────────────────────────────────
    //  index()
    // ─────────────────────────────────────────────────────────────
    public function index()
    {
        $user = Auth::user();

        if ($user->type == 'QA Manager' || $user->type == 'Director') {
            $avatarCalls = AvatarLead::where(function ($query) use ($user) {
                $query->where('QapersonId', $user->id)
                      ->where('QAstatus', 'pending')
                      ->where(function ($q) {
                          $q->where('billing_status', '!=', 'No')
                            ->orWhereNull('billing_status');
                      });
            })->orWhere('QAstatus', 'on review')->get();
        } else {
            $avatarCalls = AvatarLead::where('QapersonId', $user->id)
                ->where('QAstatus', 'pending')
                ->where(function ($q) {
                    $q->where('billing_status', '!=', 'No')
                      ->orWhereNull('billing_status');
                })
                ->get();
        }

        return view('qa-section.inline_edit', compact('avatarCalls'));
    }

   

    // ─────────────────────────────────────────────────────────────
    //  update() — inline AJAX
    // ─────────────────────────────────────────────────────────────
    public function update(Request $request, AvatarLead $avatarCall)
    {
        $pk    = $request->input('pk');
        $name  = $request->input('name');
        $value = $request->input('value');

        $model = AvatarLead::find($pk);
        if (!$model) {
            return response()->json(['error' => 'Model not found'], 404);
        }

        $value = ($value === 'null') ? null : $value;

        switch ($name) {
            case 'Isgreetings':        $model->Isgreetings        = $value; break;
            case 'Ispitch_call_about': $model->Ispitch_call_about = $value; break;
            case 'Isage':              $model->Isage              = $value; break;
            case 'Issmoker':           $model->Issmoker           = $value; break;
            case 'Ishealth1':          $model->Ishealth1          = $value; break;
            case 'Isbeneficiary':      $model->Isbeneficiary      = $value; break;
            case 'Isaccount':          $model->Isaccount          = $value; break;
            case 'Isplan':             $model->Isplan             = $value; break;
            case 'Istransfer_details': $model->Istransfer_details = $value; break;
            case 'Isxfer_consent':     $model->Isxfer_consent     = $value; break;
            case 'rebuttals':          $model->rebuttals          = $value; break;
            case 'use_of_rebuttals':   $model->use_of_rebuttals   = $value; break;
            case 'no_of_refusals':     $model->no_of_refusals     = $value; break;
            case 'billing_status':     $model->billing_status     = $value; break;
            case 'Qacomments':         $model->Qacomments         = $value; break;
            case 'QAstatus':           $model->QAstatus           = $value; break;
            case 'billable_duration':
                $model->billable_duration = ($value !== null && $value !== '') ? (int) $value : null;
                break;
            default:
                return response()->json(['error' => 'Invalid field name'], 400);
        }

        $model->save();
        return response()->json(['success' => 'Field updated successfully'], 200);
    }

    // ─────────────────────────────────────────────────────────────
    //  updateQaPerson() — assign leads
    // ─────────────────────────────────────────────────────────────
    public function updateQaPerson(Request $request)
    {
        $request->validate([
            'qaperson_id' => 'required|integer|exists:users,id',
            'start_id'    => 'nullable|integer|min:1',
            'end_id'      => 'nullable|integer|min:1|gte:start_id',
            'team_id'     => 'nullable|integer|exists:teams,id',
            'lead_count'  => 'nullable|integer|min:1|max:5000',
        ]);

        $utcMinus12Time = Carbon::now('Etc/GMT+12');
        $qapersonId     = (int) $request->qaperson_id;

        if ($request->filled('team_id') && $request->filled('lead_count')) {
            $leadIds = AvatarLead::where('QAstatus', 'pending')
                ->whereNull('QapersonId')
                ->where('team_id', (int) $request->team_id)
                ->where(function ($q) {
                    $q->where('billing_status', '!=', 'No')
                      ->orWhereNull('billing_status');
                })
                ->orderBy('id')
                ->limit((int) $request->lead_count)
                ->pluck('id');

            if ($leadIds->isEmpty()) {
                return redirect()->back()->with('error', 'No pending unassigned leads found for the selected team.');
            }

            $updated = AvatarLead::whereIn('id', $leadIds)->update([
                'QapersonId' => $qapersonId,
                'Qadate'     => $utcMinus12Time,
            ]);

            return redirect()->back()->with('success', "Assigned {$updated} lead(s) by team successfully.");
        }

        if ($request->filled('start_id') && $request->filled('end_id')) {
            $updated = AvatarLead::whereBetween('id', [(int) $request->start_id, (int) $request->end_id])
                ->whereNull('QapersonId')
                ->where('QAstatus', 'pending')
                ->where(function ($q) {
                    $q->where('billing_status', '!=', 'No')
                      ->orWhereNull('billing_status');
                })
                ->update([
                    'QapersonId' => $qapersonId,
                    'Qadate'     => $utcMinus12Time,
                ]);

            return redirect()->back()->with('success', "Assigned {$updated} lead(s) by ID range successfully.");
        }

        return redirect()->back()->with('error', 'Please use either ID range or Team assignment.');
    }

    // ─────────────────────────────────────────────────────────────
    //  getQaPersonTeams() — AJAX for reassign dropdown
    // ─────────────────────────────────────────────────────────────
    public function getQaPersonTeams(Request $request)
    {
        $qapersonId = (int) $request->qaperson_id;

        // Get team_id and count from avatar_leads (center-scoped via model global scope)
        $teamData = AvatarLead::query()
            ->select('team_id', DB::raw('count(*) as cnt'))
            ->where('QapersonId', $qapersonId)
            ->where('QAstatus', 'pending')
            ->whereNotNull('team_id')
            ->groupBy('team_id')
            ->get();

        if ($teamData->isEmpty()) {
            return response()->json([]);
        }

        // Get team names from teams table for those IDs
        $teamIds = $teamData->pluck('team_id')->toArray();
        $teamNames = DB::table('teams')
            ->whereIn('id', $teamIds)
            ->select('id', 'name')
            ->get()
            ->keyBy('id');

        // Build result with team names or fallback to ID
        $result = $teamData->map(function ($row) use ($teamNames) {
            $teamInfo = $teamNames->get($row->team_id);
            $teamName = $teamInfo ? $teamInfo->name : ('Team ' . $row->team_id);
            
            return [
                'team_id'   => (int) $row->team_id,
                'team_name' => trim($teamName) ?: ('Team ' . $row->team_id),
                'count'     => (int) $row->cnt,
            ];
        });

        return response()->json($result->toArray());
    }

    // ─────────────────────────────────────────────────────────────
    //  reassignLeads()
    // ─────────────────────────────────────────────────────────────
    public function reassignLeads(Request $request)
    {
        $request->validate([
            'assign_qaperson_id' => 'required|integer|exists:users,id',
            'remove_qaperson_id' => 'required|integer|exists:users,id',
            'lead_count'         => 'required|integer|min:1|max:5000',
            'reassign_team_id'   => 'nullable|integer|exists:teams,id',
        ]);

        $utcMinus12Time = Carbon::now('Etc/GMT+12');

        $query = AvatarLead::where('QapersonId', $request->remove_qaperson_id)
            ->where('QAstatus', 'pending')
            ->where(function ($q) {
                $q->where('billing_status', '!=', 'No')
                  ->orWhereNull('billing_status');
            });

        if ($request->filled('reassign_team_id')) {
            $query->where('team_id', $request->reassign_team_id);
        }

        $leadIds = $query->orderBy('id')
            ->limit((int) $request->lead_count)
            ->pluck('id');

        if ($leadIds->isEmpty()) {
            return redirect()->back()->with('error', 'No eligible pending leads found to reassign.');
        }

        $updated = AvatarLead::whereIn('id', $leadIds)->update([
            'QapersonId' => $request->assign_qaperson_id,
            'Qadate'     => $utcMinus12Time,
        ]);

        return redirect()->back()->with('success', "Reassigned {$updated} lead(s) successfully.");
    }

    // ─────────────────────────────────────────────────────────────
    //  edit()
    // ─────────────────────────────────────────────────────────────
    public function edit($id)
    {
        $update   = AvatarLead::find($id);
        $agents   = User::query()->forCurrentCenter()->where('type', 'Avatar')->get();
        $Qaperson = User::query()->forCurrentCenter()->whereIn('type', ['QA', 'Team Lead'])->get();
        return view('qa-section.editform', compact('update', 'agents', 'Qaperson'));
    }

    // ─────────────────────────────────────────────────────────────
    //  showQaStatsForm()
    // ─────────────────────────────────────────────────────────────
    public function showQaStatsForm(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate   = $request->input('end_date');
        $qaStats   = collect();

        if ($startDate && $endDate) {
            $startDate = Carbon::parse($startDate)->startOfDay();
            $endDate   = Carbon::parse($endDate)->endOfDay();

            $qaStats = AvatarLead::select('QaPersonId', 'QAstatus', DB::raw('count(*) as total'))
                ->whereBetween('Qadate', [$startDate, $endDate])
                ->groupBy('QaPersonId', 'QAstatus')
                ->with('qaPerson')
                ->get()
                ->groupBy('QaPersonId')
                ->map(function ($group) {
                    $stats = [
                        'Pending' => 0, 'Approved' => 0, 'Rejected' => 0,
                        'On Review' => 0, 'No Recording' => 0, 'Total' => 0,
                        'name' => optional($group->first()->qaPerson)->name ?? 'N/A',
                    ];
                    foreach ($group as $item) {
                        switch ($item->QAstatus) {
                            case 'pending':      $stats['Pending']      += $item->total; break;
                            case 'approved':     $stats['Approved']     += $item->total; break;
                            case 'rejected':     $stats['Rejected']     += $item->total; break;
                            case 'on review':    $stats['On Review']    += $item->total; break;
                            case 'no recording': $stats['No Recording'] += $item->total; break;
                        }
                        $stats['Total'] += $item->total;
                    }
                    return $stats;
                });
        }

        return view('qa-section.agent_stats', compact('qaStats', 'startDate', 'endDate'));
    }

    // ─────────────────────────────────────────────────────────────
    //  showSearchForm()
    // ─────────────────────────────────────────────────────────────
    public function showSearchForm(Request $request)
    {
        $user = Auth::user();
        if ($user->type == 'QA Manager' || $user->type == 'Director') {
            $lead = $request->has('lead_id')
                ? AvatarLead::where('lead_id', $request->input('lead_id'))->first()
                : null;
            return view('qa-section.search-lead', compact('lead'));
        }
        return redirect()->back()->with('error', 'Permission Denied');
    }

    // ─────────────────────────────────────────────────────────────
    //  Reqaupdate()
    // ─────────────────────────────────────────────────────────────
    public function Reqaupdate(Request $request)
    {
        $lead = AvatarLead::findOrFail($request->id);
        $lead->Isgreetings        = $request->Isgreetings;
        $lead->Ispitch_call_about = $request->Ispitch_call_about;
        $lead->Isage              = $request->Isage;
        $lead->Issmoker           = $request->Issmoker;
        $lead->Ishealth1          = $request->Ishealth1;
        $lead->Isbeneficiary      = $request->Isbeneficiary;
        $lead->Isaccount          = $request->Isaccount;
        $lead->Istransfer_details = $request->Istransfer_details;
        $lead->Isxfer_consent     = $request->Isxfer_consent;
        $lead->rebuttals          = $request->rebuttals;
        $lead->use_of_rebuttals   = $request->use_of_rebuttals;
        $lead->no_of_refusals     = $request->no_of_refusals;
        $lead->Isplan             = $request->Isplan;
        $lead->QAstatus           = $request->QAstatus;
        $lead->Qacomments         = $request->Qacomments;
        $lead->save();
        return redirect()->back()->with('success', 'ReQa Successful');
    }

    // ─────────────────────────────────────────────────────────────
    //  getNoRecordingLeads()
    // ─────────────────────────────────────────────────────────────
    public function getNoRecordingLeads()
    {
        try {
            $leads = AvatarLead::whereNull('recording')
                ->orWhere('QAstatus', 'no recording')
                ->orderBy('created_at', 'desc')
                ->select(['id', 'lead_id', 'recording', 'QAstatus', 'created_at', 'dialer_id', 'agent_name'])
                ->paginate(50);
            return view('qa-section.no-rec-leads', compact('leads'));
        } catch (\Exception $e) {
            Log::error('Error in getNoRecordingLeads: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while loading the page.');
        }
    }

    // ─────────────────────────────────────────────────────────────
    //  noRecordingUpdate()
    // ─────────────────────────────────────────────────────────────
    public function noRecordingUpdate(Request $request)
    {
        try {
            DB::beginTransaction();

            AvatarLead::findOrFail($request->id)->update([
                'recording'          => $request->recording,
                'QAstatus'           => 'pending',
                'recording_link'     => $request->recording_link,
                'recording_filename' => $request->recording_filename,
            ]);

            $recording = Recording::where('lead_id', $request->lead_id)->firstOrFail();
            $recording->update([
                'recording_filename' => $request->recording_filename,
                'recording_link'     => $request->recording,
            ]);

            DB::commit();
            return redirect('/no-rec-leads')->with('success', 'Recording updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating recording: ' . $e->getMessage());
            return redirect('/no-rec-leads')->with('error', 'Failed to update recording. Please try again.');
        }
    }
}