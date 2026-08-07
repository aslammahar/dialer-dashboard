<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SalesCloser;
use App\Models\SalesClient;
use App\Models\SalesCarrier;
use App\Models\SalesTeam;
use App\Models\DailySalesEntry;

class DailySalesController extends Controller
{
    protected function assertEditor()
{
    abort_unless(
        auth()->check() && in_array(auth()->user()->email, [
            'fazail@jsonscommunications.com',
           'm.muzammil@jsonscommunication.com',
    'ubaid.khan@jsonscommunication.com',
    'hussamjanjua@jsons.com',
    'furqankashif@jsons.com',
    'sheikh.noman@jsonscommunication.com',
    'taimoorjanjua@mgmt.jsonscommunications.com',
    'aslambaig@jsons.com',
        ]),
        403,
        'You do not have permission to make changes.'
    );
}

    // public function create()
    // {
    //     return view('daily-sales.create', [
    //         'closers'        => SalesCloser::with('team')->where('active', true)->orderBy('name')->get(),
    //         'teams'          => SalesTeam::orderBy('name')->get(),
    //         'clients'        => SalesClient::orderBy('name')->get(),
    //         'carriers'       => SalesCarrier::orderBy('name')->get(),
    //         'pendingEntries' => DailySalesEntry::with(['closer', 'client', 'carrier'])
    //             ->where('status', 'pending')
    //             ->latest('entry_date')
    //             ->get(),
    //     ]);
    // }
   public function create()
{
    return view('daily-sales.create', [
        'closers'        => SalesCloser::with('team')->where('active', true)->orderBy('name')->get(),
        'teams'          => SalesTeam::orderBy('name')->get(),
        'clients'        => SalesClient::orderBy('name')->get(),
        'carriers'       => SalesCarrier::orderBy('name')->get(),
        'pendingEntries' => DailySalesEntry::with(['closer', 'client', 'carrier'])
            ->where(function ($q) {
                $q->where('status', 'pending')
                  ->orWhere(function ($q2) {
                      $q2->where('status', 'approved')
                         ->where('entry_date', '>=', now('America/New_York')->subDays(3)->toDateString());
                  });
            })
            ->latest('entry_date')
            ->get(),
    ]);
}

    public function store(Request $request)
    {
        $data = $request->validate([
            'entry_date'        => ['required', 'date'],
            'sales_closer_id'   => ['required', 'exists:sales_closers,id'],
            'sales_team_id'     => ['nullable', 'exists:sales_teams,id'],
            'sales_client_id'   => ['nullable', 'exists:sales_clients,id'],
            'sales_carrier_id'  => ['nullable', 'exists:sales_carriers,id'],
            'leads_id'          => ['nullable', 'string', 'max:100'],
            'status'            => ['required', 'in:approved,pending'],
            'sale_type'         => ['nullable', 'in:level,gi'],
            'avg_pre'           => ['nullable', 'numeric'],
            'notes'             => ['nullable', 'string', 'max:255'],
        ]);

        $data['created_by'] = auth()->id();

        $entry = DailySalesEntry::create($data);

        $redirect = redirect()
            ->route('dialer-dashboard')
            ->with('status', 'Sale entry added successfully.');

        if ($entry->status === 'approved') {
            $redirect->with('celebrate_closer', $entry->closer->name);
        }

        return $redirect;
    }
public function update(Request $request, DailySalesEntry $entry)
{
    $this->assertEditor();

    $entryDate = \Carbon\Carbon::parse($entry->entry_date)->startOfDay();
    $deadline  = $entryDate->copy()->addDay()->endOfDay(); // sale ki apni date + agla 1 din tak edit allowed

    if (now('America/New_York')->greaterThan($deadline)) {
        abort(403, 'This sale is older than 1 day and can no longer be edited.');
    }

    $data = $request->validate([
        'entry_date' => ['required', 'date'],
        'leads_id'   => ['nullable', 'string', 'max:100'],
        'sale_type'  => ['nullable', 'in:level,gi'],
        'avg_pre'    => ['nullable', 'numeric'],
        'status'     => ['required', 'in:approved,pending'],
    ]);

    $entry->update($data);

    return redirect()
        ->route('daily-sales.create')
        ->with('status', $entry->status === 'approved' ? 'Sale approved.' : 'Sale entry updated.');
}

public function destroy(DailySalesEntry $entry)
{
    $this->assertEditor();

    $entry->delete();

    return redirect()
        ->route('daily-sales.create')
        ->with('status', 'Sale entry removed.');
}
    // public function update(Request $request, DailySalesEntry $entry)
    // {
    //     $this->assertEditor();

    //     $data = $request->validate([
    //         'leads_id'  => ['nullable', 'string', 'max:100'],
    //         'sale_type' => ['nullable', 'in:level,gi'],
    //         'avg_pre'   => ['nullable', 'numeric'],
    //         'status'    => ['required', 'in:approved,pending'],
    //     ]);

    //     $entry->update($data);

    //     return redirect()
    //         ->route('daily-sales.create')
    //         ->with('status', $entry->status === 'approved' ? 'Sale approved.' : 'Sale entry updated.');
    // }
}