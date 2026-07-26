<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SalesTeam;
use App\Models\SalesCloser;
use App\Models\SalesClient;
use App\Models\SalesCarrier;

class SalesLookupController extends Controller
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
        ]),
        403,
        'You do not have permission to make changes.'
    );
}
   public function storeTeam(Request $request)
{
    $this->assertEditor();

    $data = $request->validate([
        'name'   => ['required', 'string', 'max:255', 'unique:sales_teams,name'],
        'target' => ['nullable', 'integer', 'min:0'],
    ]);

    \App\Models\SalesTeam::create([
        'name'   => $data['name'],
        'target' => $data['target'] ?? 0,
    ]);

    return back()->with('status', "Team \"{$data['name']}\" created.");
}

public function setTeamTarget(Request $request)
{
    $this->assertEditor();

    $data = $request->validate([
        'team_id' => ['required', 'exists:sales_teams,id'],
        'target'  => ['required', 'integer', 'min:0'],
    ]);

    $team = \App\Models\SalesTeam::find($data['team_id']);
    $team->update(['target' => $data['target']]);

    return redirect()
        ->route('sales-target.edit')
        ->with('status', "{$team->name}'s target updated to {$data['target']}.");
}

public function setClientTarget(Request $request)
    {
        $this->assertEditor();

        $data = $request->validate([
            'client_id' => ['required', 'exists:sales_clients,id'],
            'target'    => ['required', 'integer', 'min:0'],
        ]);

        $client = SalesClient::find($data['client_id']);
        $client->update(['target' => $data['target']]);

        return redirect()->route('sales-target.edit')->with('status', "{$client->name}'s target updated to {$data['target']}.");
    }

    public function setCarrierTarget(Request $request)
    {
        $this->assertEditor();

        $data = $request->validate([
            'carrier_id' => ['required', 'exists:sales_carriers,id'],
            'target'     => ['required', 'integer', 'min:0'],
        ]);

        $carrier = SalesCarrier::find($data['carrier_id']);
        $carrier->update(['target' => $data['target']]);

        return redirect()->route('sales-target.edit')->with('status', "{$carrier->name}'s target updated to {$data['target']}.");
    }

    // ---------- REMOVE ----------

    public function destroyTeam(SalesTeam $team)
    {
        $this->assertEditor();

        SalesCloser::where('sales_team_id', $team->id)->update(['sales_team_id' => null]);
        $team->delete();

        return back()->with('status', "Team \"{$team->name}\" removed.");
    }

    public function destroyClient(SalesClient $client)
    {
        $this->assertEditor();
        $client->delete();

        return back()->with('status', "Client \"{$client->name}\" removed.");
    }

    public function destroyCarrier(SalesCarrier $carrier)
    {
        $this->assertEditor();
        $carrier->delete();

        return back()->with('status', "Carrier \"{$carrier->name}\" removed.");
    }

    public function storeCloser(Request $request)
{
    $this->assertEditor();

    $data = $request->validate([
        'name'          => ['required', 'string', 'max:255'],
        'sales_team_id' => ['nullable', 'exists:sales_teams,id'],
    ]);

    $exists = SalesCloser::whereRaw('LOWER(TRIM(name)) = ?', [strtolower(trim($data['name']))])->exists();

    if ($exists) {
        return back()
            ->withErrors(['name' => "A closer named \"{$data['name']}\" already exists."])
            ->withInput();
    }

    SalesCloser::create($data + ['active' => true]);

    return back()->with('status', "Closer \"{$data['name']}\" created.");
}

    public function storeClient(Request $request)
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:255', 'unique:sales_clients,name']]);
        SalesClient::create($data);

        return back()->with('status', "Client \"{$data['name']}\" created.");
    }

    public function storeCarrier(Request $request)
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:255', 'unique:sales_carriers,name']]);
        SalesCarrier::create($data);

        return back()->with('status', "Carrier \"{$data['name']}\" created.");
    }
}