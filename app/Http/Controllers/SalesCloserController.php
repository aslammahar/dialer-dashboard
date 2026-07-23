<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SalesCloser;
use App\Models\SalesTeam;

class SalesCloserController extends Controller
{
//     protected function assertEditor()
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
    public function index()
    {
        return view('sales-closers.index', [
            'closers' => SalesCloser::with('team')->orderBy('name')->get(),
            'teams'   => SalesTeam::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, SalesCloser $closer)
    {
        $data = $request->validate([
            'sales_team_id' => ['nullable', 'exists:sales_teams,id'],
        ]);

        $closer->update(['sales_team_id' => $data['sales_team_id'] ?? null]);

        return back()->with('status', "{$closer->name}'s team updated to ".($closer->team->name ?? 'None').".");
    }

    public function destroy(SalesCloser $closer)
{
    // $this->assertEditor();

    $closer->delete();

    return back()->with('status', "{$closer->name} removed.");
}
}