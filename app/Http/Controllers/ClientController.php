<?php

namespace App\Http\Controllers;

use App\Models\ClientDeal;
use App\Models\ClientPermission;
use App\Models\Contract;
use App\Models\CustomField;
use App\Models\Estimation;
use App\Models\Invoice;
use App\Models\Plan;
use App\Models\User;
use App\Models\Client;
use Illuminate\Support\Facades\Validator;

use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;

class ClientController extends Controller
{
    public function __construct()
    {
        $this->middleware(
            [
                'auth',
                'XSS',
            ]
        );
    }


    public function showChildren($clientId)
    {
        $client = User::find($clientId);

        if (!$client) {
            abort(404); // Or redirect with an error message
        }

        // Find children based on the client_id field
        $children = User::where('client_id', $clientId)->get();

        return view('clients.children', compact('client', 'children'));
    }

    public function index()
    {
        if (\Auth::user()->can('manage client')) {
            $user = \Auth::user();

            // Get parent clients (is_parent = 1)
            $clients = Client::all();


            return view('clients.index', compact('clients'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }
    public function reactivate($id)
    {
        try {
            if (\Auth::user()->can('edit user')) {
                $user = User::findOrFail($id);
                $user->last_login_at = now();
                $user->save();

                return redirect()->route('clients.index')->with('success', __('Client has been reactivated successfully.'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('An error occurred: ') . $e->getMessage() . ' on line ' . $e->getLine() . ' in ' . $e->getFile());
        }
    }


    public function create()
    {
        if (Auth::user()->can('create client')) {
            $parentClients = Client::all()->pluck('name', 'id');
            return view('clients.create', compact('parentClients'));
        } else {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
            'client_type' => 'required|in:parent,child',
            'parent_id' => 'nullable|exists:clients,id', // Now referring directly to the clients table
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->getMessageBag()->first());
        }

        // Create a new user
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->type = 'client';
        $user->is_parent = $request->client_type === 'parent' ? 1 : 0;
        $user->client_id = $request->client_type === 'child' ? $request->parent_id : null; // Store selected parent client ID
        $user->save();

        // Only store parent clients in the clients table
        if ($request->client_type === 'parent') {
            $client = new Client();
            $client->name = $request->name;
            $client->email = $request->email;
            $client->save();
        }

        return redirect()->route('clients.index')->with('success', __('Client successfully added.'));
    }





    public function show(User $client)
    {
        $usr = Auth::user();
        if (!empty($client) && $usr->id == $client->creatorId() && $client->id != $usr->id && $client->type == 'client') {
            // For Estimations
            $estimations = $client->clientEstimations()->orderByDesc('id')->get();
            $curr_month  = $client->clientEstimations()->whereMonth('issue_date', '=', date('m'))->get();
            $curr_week   = $client->clientEstimations()->whereBetween(
                'issue_date',
                [
                    \Carbon\Carbon::now()->startOfWeek(),
                    \Carbon\Carbon::now()->endOfWeek(),
                ]
            )->get();
            $last_30days = $client->clientEstimations()->whereDate('issue_date', '>', \Carbon\Carbon::now()->subDays(30))->get();
            // Estimation Summary
            $cnt_estimation                = [];
            $cnt_estimation['total']       = Estimation::getEstimationSummary($estimations);
            $cnt_estimation['this_month']  = Estimation::getEstimationSummary($curr_month);
            $cnt_estimation['this_week']   = Estimation::getEstimationSummary($curr_week);
            $cnt_estimation['last_30days'] = Estimation::getEstimationSummary($last_30days);

            $cnt_estimation['cnt_total']       = $estimations->count();
            $cnt_estimation['cnt_this_month']  = $curr_month->count();
            $cnt_estimation['cnt_this_week']   = $curr_week->count();
            $cnt_estimation['cnt_last_30days'] = $last_30days->count();

            // For Contracts
            $contracts   = $client->clientContracts()->orderByDesc('id')->get();
            $curr_month  = $client->clientContracts()->whereMonth('start_date', '=', date('m'))->get();
            $curr_week   = $client->clientContracts()->whereBetween(
                'start_date',
                [
                    \Carbon\Carbon::now()->startOfWeek(),
                    \Carbon\Carbon::now()->endOfWeek(),
                ]
            )->get();
            $last_30days = $client->clientContracts()->whereDate('start_date', '>', \Carbon\Carbon::now()->subDays(30))->get();

            // Contracts Summary
            $cnt_contract                = [];
            $cnt_contract['total']       = Contract::getContractSummary($contracts);
            $cnt_contract['this_month']  = Contract::getContractSummary($curr_month);
            $cnt_contract['this_week']   = Contract::getContractSummary($curr_week);
            $cnt_contract['last_30days'] = Contract::getContractSummary($last_30days);

            $cnt_contract['cnt_total']       = $contracts->count();
            $cnt_contract['cnt_this_month']  = $curr_month->count();
            $cnt_contract['cnt_this_week']   = $curr_week->count();
            $cnt_contract['cnt_last_30days'] = $last_30days->count();

            return view('clients.show', compact('client', 'estimations', 'cnt_estimation', 'contracts', 'cnt_contract'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function edit(User $client)
    {
        if (\Auth::user()->can('edit client')) {
            $user = \Auth::user();
            if ($client->created_by == $user->creatorId()) {
                $client->customField = CustomField::getData($client, 'client');
                $customFields        = CustomField::where('module', '=', 'client')->get();

                return view('clients.edit', compact('client', 'customFields'));
            } else {
                return response()->json(['error' => __('Invalid Client.')], 401);
            }
        } else {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

    public function update(User $client, Request $request)
    {
        if (\Auth::user()->can('edit client')) {
            $user = \Auth::user();
            if ($client->created_by == $user->creatorId()) {
                $validation = [
                    'name' => 'required',
                    'email' => 'required|email|unique:users,email,' . $client->id,
                ];

                $post         = [];
                $post['name'] = $request->name;
                if (!empty($request->password)) {
                    $validation['password'] = 'required';
                    $post['password']       = Hash::make($request->password);
                }

                $validator = \Validator::make($request->all(), $validation);
                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }
                $post['email'] = $request->email;

                $client->update($post);

                CustomField::saveData($client, $request->customField);

                return redirect()->back()->with('success', __('Client Updated Successfully!'));
            } else {
                return redirect()->back()->with('error', __('Invalid Client.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function destroy(User $client)
    {
        $user = \Auth::user();
        if ($client->created_by == $user->creatorId()) {
            $estimation = Estimation::where('client_id', '=', $client->id)->first();
            if (empty($estimation)) {
                // Log client deletion
                Log::info('Client deleted', [
                    'deleted_by' => $user->email,
                    'deleted_by_id' => $user->id,
                    'client_id' => $client->id,
                    'client_email' => $client->email,
                    'client_name' => $client->name,
                    'ip' => request()->ip(),
                    'timestamp' => now()
                ]);

                /*  ClientDeal::where('client_id', '=', $client->id)->delete();
                    ClientPermission::where('client_id', '=', $client->id)->delete();*/
                $client->delete();
                return redirect()->back()->with('success', __('Client Deleted Successfully!'));
            } else {
                return redirect()->back()->with('error', __('This client has assigned some estimation.'));
            }
        } else {
            return redirect()->back()->with('error', __('Invalid Client.'));
        }
    }

    public function clientPassword($id)
    {
        try {
            // Check if ID is provided
            if (empty($id)) {
                return redirect()->back()->with('error', __('Invalid user ID.'));
            }

            // Try to find the user
            $user = User::find($id);
            
            if (!$user) {
                return redirect()->back()->with('error', __('User not found.'));
            }

            // Find client created by this user
            $client = User::where('created_by', '=', $user->id)->where('type', '=', 'client')->first();

            return view('clients.reset', compact('user', 'client'));
        } catch (\Exception $e) {
            Log::error('Error in clientPassword method: ' . $e->getMessage(), [
                'id' => $id,
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return redirect()->back()->with('error', __('An error occurred. Please try again.'));
        }
    }

    public function clientPasswordReset(Request $request, $id)
    {
        $validator = \Validator::make(
            $request->all(),
            [
                'password' => 'required|confirmed|same:password_confirmation',
            ]
        );

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }


        $user                 = User::where('id', $id)->first();
        $user->forceFill([
            'password' => Hash::make($request->password),
        ])->save();

        return redirect()->route('clients.index')->with(
            'success',
            'Client Password successfully updated.'
        );
    }
}
