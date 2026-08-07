<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SalesTarget;

class SalesTargetController extends Controller
{
    // protected function assertEditor()
//     protected function canEdit()
// {
//     abort_unless(
//         auth()->check() && in_array(auth()->user()->email, [
//             'fazail@jsonscommunications.com',
//             'REPLACE_WITH_SECOND_PERSON_EMAIL@example.com',
//         ]),
//         403,
//         'You do not have permission to make changes.'
//     );
// }


 protected function canEdit()
{
    return auth()->check() && in_array(auth()->user()->email, [
         'fazail@jsonscommunications.com',
            'm.muzammil@jsonscommunication.com',
            'ubaid.khan@jsonscommunication.com',
            'hussamjanjua@jsons.com',
            'furqankashif@jsons.com',
            'sheikh.noman@jsonscommunication.com',
            'taimoorjanjua@mgmt.jsonscommunications.com',
            'aslambaig@jsons.com',
    ]);
}
    
   public function edit()
{
    $monthStart = now()->startOfMonth()->toDateString();

    $target = SalesTarget::firstOrNew(
        ['month' => $monthStart],
        [
            'spd_target'         => 2.0,
            'monthly_spd_target' => 2.5,
            'raw_target'         => 40,
            'reward_headline'    => 'the whole team earns a trip',
            'milestone_1_label'  => 'Movie Night for Closers',
            'milestone_2_label'  => 'Cash Bonus',
            'milestone_2_amount' => '100k',
            'milestone_3_label'  => 'Team Trip',
        ]
    );

    return view('sales-target.edit', [
        'target'     => $target,
        'monthStart' => $monthStart,
        'teams'      => \App\Models\SalesTeam::orderBy('name')->get(), // new
        'clients'  => \App\Models\SalesClient::orderBy('name')->get(),
'carriers' => \App\Models\SalesCarrier::orderBy('name')->get(),
        'canEdit'    => $this->canEdit(),
    ]);
}

    public function update(Request $request)
{
    abort_unless($this->canEdit(), 403, 'You do not have permission to make changes.');

    $data = $request->validate([
        'month'               => ['required', 'date'],
        'raw_target'          => ['required', 'integer', 'min:1'],
        'spd_target'          => ['required', 'numeric', 'min:0'],
        'monthly_spd_target'  => ['required', 'numeric', 'min:0'],
        'reward_headline'     => ['required', 'string', 'max:255'],
        'milestone_1_label'   => ['required', 'string', 'max:255'],
        'milestone_2_label'   => ['required', 'string', 'max:255'],
        'milestone_2_amount'  => ['nullable', 'string', 'max:50'],
        'milestone_3_label'   => ['required', 'string', 'max:255'],
    ]);

    SalesTarget::updateOrCreate(['month' => $data['month']], $data);

    return redirect()
        ->route('dialer-dashboard')
        ->with('status', 'Sales target updated.');
}
}