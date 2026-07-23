<?php

namespace App\Http\Controllers;

use App\Models\DialersList;
use App\Models\DialersServer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class DialersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $dialerLists = DialersList::orderBy('created_at', 'desc')->get();
            $dialerServers = DialersServer::orderBy('created_at', 'desc')->get();
        
        return view('dialers.index', compact('dialerLists', 'dialerServers'));
        } catch (\Exception $e) {
            Log::error('DialersController@index - Error fetching dialers', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to load dialers. Please try again.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
        return view('dialers.create');
        } catch (\Exception $e) {
            Log::error('DialersController@create - Error loading create form', [
                'error' => $e->getMessage()
            ]);

            return redirect()->route('dialers.index')
                ->with('error', 'Failed to load create form.');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'dialer_ip' => 'required|string|max:150',
                'dialer_weblink' => 'required|string|max:150',
                'dialer_access' => 'required|string|max:300',
                'dialer_no' => 'required|string|max:40',
                'dialer_team' => 'required|string|max:40',
                'recording_link' => 'nullable|string|url',
            'dialer_name' => 'required|string',
            'server_no' => 'required|string',
                'server_ip' => 'required|string|ip',
            'folder_name' => 'required|string',
            'server_status' => 'sometimes|boolean',
            ], [
                'dialer_ip.required' => 'Dialer IP is required.',
                'dialer_weblink.required' => 'Dialer web link is required.',
                'dialer_access.required' => 'Dialer access is required.',
                'dialer_no.required' => 'Dialer number is required.',
                'dialer_team.required' => 'Dialer team is required.',
                'dialer_name.required' => 'Dialer name is required.',
                'server_no.required' => 'Server number is required.',
                'server_ip.required' => 'Server IP is required.',
                'server_ip.ip' => 'Server IP must be a valid IP address.',
                'folder_name.required' => 'Folder name is required.',
            ]);

            DB::beginTransaction();

            try {
            // Create Dialer List
                $dialerList = DialersList::create([
                    'dialer_ip' => $validated['dialer_ip'],
                    'dialer_weblink' => $validated['dialer_weblink'],
                    'dialer_access' => $validated['dialer_access'],
                    'dialer_no' => $validated['dialer_no'],
                    'dialer_team' => $validated['dialer_team'],
                    'recording_link' => $validated['recording_link'] ?? null,
            ]);

                // Create Dialer Server
                $dialerServer = DialersServer::create([
                    'dialer_name' => $validated['dialer_name'],
                    'server_no' => $validated['server_no'],
                    'server_ip' => $validated['server_ip'],
                    'recording_link' => $validated['recording_link'] ?? null,
                    'folder_name' => $validated['folder_name'],
                    'server_status' => $request->has('server_status') && $request->server_status ? '1' : '0',
            ]);

                DB::commit();

                Log::info('DialersController@store - Dialer created successfully', [
                    'dialer_list_id' => $dialerList->id,
                    'dialer_server_id' => $dialerServer->id,
                    'dialer_no' => $validated['dialer_no']
                ]);

        return redirect()->route('dialers.index')
            ->with('success', 'Dialer created successfully.');
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            Log::error('DialersController@store - Error creating dialer', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'request_data' => $request->except(['_token', 'password'])
            ]);

            return redirect()->back()
                ->with('error', 'Failed to create dialer. Please try again.')
                ->withInput();
        }
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
        $dialerList = DialersList::findOrFail($id);

            // Try to find related server - they might be linked by dialer_no or server_no
            // Since there's no direct foreign key, we'll need to pass both IDs or find by dialer_no
            $dialerServer = DialersServer::where('server_no', $dialerList->dialer_no)
                ->orWhere('dialer_name', 'like', '%' . $dialerList->dialer_no . '%')
                ->first();

            // If no server found, create a new empty instance so the view always has the variable
            if (!$dialerServer) {
                $dialerServer = new DialersServer();
                $dialerServer->dialer_name = '';
                $dialerServer->server_no = '';
                $dialerServer->server_ip = '';
                $dialerServer->folder_name = '';
                $dialerServer->recording_link = '';
                $dialerServer->server_status = '0';
            }
        
        return view('dialers.edit', compact('dialerList', 'dialerServer'));
        } catch (\Exception $e) {
            Log::error('DialersController@edit - Error loading edit form', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('dialers.index')
                ->with('error', 'Failed to load dialer for editing.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                // DialersList fields - all optional, update only what's provided
                'dialer_ip' => 'nullable|string|max:150',
                'dialer_weblink' => 'nullable|string|max:150',
                'dialer_access' => 'nullable|string|max:300',
                'dialer_no' => 'nullable|string|max:40',
                'dialer_team' => 'nullable|string|max:40',
                'recording_link' => 'nullable|string|max:500',
                // DialersServer fields - all optional, update only what's provided
                'dialer_name' => 'nullable|string',
                'server_no' => 'nullable|string',
                'server_ip' => 'nullable|string|max:45',
                'folder_name' => 'nullable|string',
                'server_status' => 'sometimes|boolean',
        ]);

            DB::beginTransaction();

            try {
                // Get DialersList by ID (from route)
            $dialerList = DialersList::findOrFail($id);
                
                // Store OLD dialer_ip before update (to find server)
                $oldDialerIp = $dialerList->dialer_ip;
                
                // Update DialersList - only update fields that are provided
                $listUpdateData = [];
                if (!empty($validated['dialer_ip'])) {
                    $listUpdateData['dialer_ip'] = $validated['dialer_ip'];
                }
                if (!empty($validated['dialer_weblink'])) {
                    $listUpdateData['dialer_weblink'] = $validated['dialer_weblink'];
                }
                if (!empty($validated['dialer_access'])) {
                    $listUpdateData['dialer_access'] = $validated['dialer_access'];
                }
                if (!empty($validated['dialer_no'])) {
                    $listUpdateData['dialer_no'] = $validated['dialer_no'];
                }
                if (!empty($validated['dialer_team'])) {
                    $listUpdateData['dialer_team'] = $validated['dialer_team'];
                }
                if (isset($validated['recording_link'])) {
                    $listUpdateData['recording_link'] = $validated['recording_link'];
                }
                
                if (!empty($listUpdateData)) {
                    $dialerList->fill($listUpdateData);
                    $dialerListSaved = $dialerList->save();
                    
                    if (!$dialerListSaved) {
                        throw new \Exception('Failed to save DialerList');
                    }
                    
                    Log::info('DialersController@update - DialerList updated', [
                        'id' => $dialerList->id,
                        'updated_fields' => array_keys($listUpdateData)
                    ]);
                }

                // Update DialersServer - only if server exists and fields are provided
                // Find server by OLD dialer_ip matching server_ip
                $dialerServer = DialersServer::where('server_ip', $oldDialerIp)->first();
                
                if ($dialerServer) {
                    // Server exists - update only fields that are provided
                    $serverUpdateData = [];
                    
                    if (!empty($validated['dialer_name'])) {
                        $serverUpdateData['dialer_name'] = $validated['dialer_name'];
                    }
                    if (!empty($validated['server_no'])) {
                        $serverUpdateData['server_no'] = $validated['server_no'];
                    }
                    if (!empty($validated['server_ip'])) {
                        $serverUpdateData['server_ip'] = $validated['server_ip'];
                    }
                    if (!empty($validated['folder_name'])) {
                        $serverUpdateData['folder_name'] = $validated['folder_name'];
                    }
                    if (isset($validated['recording_link'])) {
                        $serverUpdateData['recording_link'] = $validated['recording_link'];
                    }
                    if ($request->has('server_status')) {
                        $serverUpdateData['server_status'] = $request->server_status ? '1' : '0';
                    }
                    
                    if (!empty($serverUpdateData)) {
                        $dialerServer->fill($serverUpdateData);
                        $serverSaved = $dialerServer->save();
                        
                        if (!$serverSaved) {
                            throw new \Exception('Failed to save DialersServer');
                        }
                        
                        Log::info('DialersController@update - DialersServer updated', [
                            'id' => $dialerServer->id,
                            'updated_fields' => array_keys($serverUpdateData)
                        ]);
                    }
                } else {
                    // Server NOT found - do NOT create, just skip
                    Log::info('DialersController@update - DialersServer not found, skipping update', [
                        'dialer_ip' => $oldDialerIp
                    ]);
                }

                DB::commit();

                Log::info('DialersController@update - Dialer updated successfully', [
                    'dialer_list_id' => $dialerList->id,
                    'dialer_server_id' => $dialerServer->id,
                    'dialer_no' => $validated['dialer_no']
                ]);

        return redirect()->route('dialers.index')
            ->with('success', 'Dialer updated successfully.');
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            Log::error('DialersController@update - Error updating dialer', [
                'id' => $id,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'request_data' => $request->except(['_token', 'password'])
            ]);

            return redirect()->back()
                ->with('error', 'Failed to update dialer. Please try again.')
                ->withInput();
    }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            try {
            $dialerList = DialersList::findOrFail($id);

                // Try to find and delete related server
                $dialerServer = DialersServer::where('server_no', $dialerList->dialer_no)
                    ->orWhere('dialer_name', 'like', '%' . $dialerList->dialer_no . '%')
                    ->first();
            
            $dialerList->delete();

                if ($dialerServer) {
            $dialerServer->delete();
                }

                DB::commit();

                Log::info('DialersController@destroy - Dialer deleted successfully', [
                    'dialer_list_id' => $id,
                    'dialer_server_id' => $dialerServer->id ?? null
                ]);

        return redirect()->route('dialers.index')
            ->with('success', 'Dialer deleted successfully.');
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (\Exception $e) {
            Log::error('DialersController@destroy - Error deleting dialer', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('dialers.index')
                ->with('error', 'Failed to delete dialer. Please try again.');
        }
    }
}
