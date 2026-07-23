<?php

namespace App\Http\Controllers;

use App\Models\OurCampaign;
use App\Models\User;

use App\Models\Client;

use App\Models\CampaignResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CampaignResponseController extends Controller
{
    public function index(OurCampaign $our_campaign)
{
    $user = auth()->user();

    $query = $our_campaign->responses();

    if ($user->type === 'client') {
        // Only show responses where refer_to matches the client's ID
        $query->where('refer_to', $user->id);
    }

    $responses = $query->paginate(10);

    return view('campaign_responses.index', compact('our_campaign', 'responses'));
}


public function show(OurCampaign $our_campaign, CampaignResponse $campaign_response)
{
    // Debug the data being passed
    \Log::info('Campaign Response Data:', [
        'campaign_id' => $our_campaign->id,
        'response_id' => $campaign_response->id,
        'form_fields_count' => $our_campaign->newFormFields()->count()
    ]);

    $formFields = $our_campaign->newFormFields()->get();
    
    return view('campaign_responses.show', compact('our_campaign', 'campaign_response', 'formFields'));
}

    public function edit(OurCampaign $our_campaign, CampaignResponse $campaign_response)
    {
        if ($campaign_response->campaign_id !== $our_campaign->id) {
            abort(404);
        }
    
        $allFields = $our_campaign->newFormFields->whereIn('field_role', [ 'qa']);
        $submittedData = $campaign_response->submission_data ?? [];
    
        $fieldsGroupedByRole = $allFields->groupBy('field_role');
    
        return view('campaign_responses.edit', compact('our_campaign', 'campaign_response', 'fieldsGroupedByRole', 'submittedData'));
    }

    public function update(Request $request, OurCampaign $our_campaign, CampaignResponse $campaign_response)
{
    try {
        if ($campaign_response->campaign_id !== $our_campaign->id) {
            abort(404);
        }

        // Get QA fields
        $fields = $our_campaign->newFormFields->where('field_role', 'qa');
        
        // Log incoming request data
        Log::info('Incoming request data:', [
            'request_all' => $request->all(),
            'fields' => $fields->toArray()
        ]);

        if ($fields->isEmpty()) {
            return redirect()
                ->back()
                ->with('error', 'No QA fields found for this campaign.');
        }

        // Build validation rules for QA fields
        $rules = [];
        foreach ($fields as $field) {
            // Use the qa[] prefix as per the form structure
            $key = 'qa.' . $field->name;
            $rules[$key] = $field->required ? 'required' : 'nullable';
            
            if ($field->type === 'email') {
                $rules[$key] .= '|email';
            } elseif ($field->type === 'number') {
                $rules[$key] .= '|numeric';
            }
        }

        // Log validation rules
        Log::info('Validation rules:', ['rules' => $rules]);

        // Validate the request
        $validatedData = $request->validate($rules);

        // Get only the QA data from validated data
        $qaData = $validatedData['qa'] ?? [];
        
        // Get existing submission data
        $existingData = $campaign_response->submission_data ?? [];
        
        // Log data before merge
        Log::info('Before merge:', [
            'existing_data' => $existingData,
            'qa_data' => $qaData
        ]);

        // Merge existing data with new QA data
        $updatedData = array_merge($existingData, $qaData);
        
        // Log final data
        Log::info('After merge:', ['updated_data' => $updatedData]);

        DB::beginTransaction();
        try {
            $campaign_response->submission_data = $updatedData;
            $campaign_response->save();

            DB::commit();

            return redirect()
                ->route('our_campaigns.responses.index', $our_campaign)
                ->with('success', 'Response updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Database error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->back()
                ->with('error', 'Database error: ' . $e->getMessage())
                ->withInput();
        }

    } catch (\Exception $e) {
        Log::error('General error:', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return redirect()
            ->back()
            ->with('error', 'Error: ' . $e->getMessage())
            ->withInput();
    }
}





public function admin_edit(OurCampaign $our_campaign, CampaignResponse $campaign_response)
{
    if ($campaign_response->campaign_id !== $our_campaign->id) {
        abort(404);
    }

    $allFields = $our_campaign->newFormFields->whereIn('field_role', ['admin']);
    $submittedData = $campaign_response->submission_data ?? [];
    $fieldsGroupedByRole = $allFields->groupBy('field_role');

    // Fetch all clients who are parents
    $parentClients = Client::all();



    return view('campaign_responses.admin-edit', compact(
        'our_campaign',
        'campaign_response',
        'fieldsGroupedByRole',
        'submittedData',
        'parentClients'
    ));
}

public function getChildClients($clientId)
{
    // Fetch users where type = 'client' and client_id matches parent ID
    $childClients = User::where('type', 'client')->where('client_id', $clientId)->get();

    return response()->json($childClients);
}


public function admin_update(Request $request, OurCampaign $our_campaign, CampaignResponse $campaign_response)
{
    try {
        if ($campaign_response->campaign_id !== $our_campaign->id) {
            abort(404);
        }

        $fields = $our_campaign->newFormFields->where('field_role', 'admin');

        if ($fields->isEmpty()) {
            return redirect()->back()->with('error', 'No Admin fields found for this campaign.');
        }

        $rules = [];
        foreach ($fields as $field) {
            $key = $field->name;
            $rules[$key] = $field->required ? 'required' : 'nullable';

            if ($field->type === 'email') {
                $rules[$key] .= '|email';
            } elseif ($field->type === 'number') {
                $rules[$key] .= '|numeric';
            }
        }

        // Validation for refer_to if a child is selected
        if ($request->has('child')) {
            $rules['refer_to'] = 'nullable|integer';
        }

        $validatedData = $request->validate($rules);

        $existingData = $campaign_response->submission_data ?? [];
        $updatedData = array_merge($existingData, $validatedData);

        DB::beginTransaction();

        try {
            $campaign_response->submission_data = $updatedData;

            if ($request->has('child') && $request->input('child')) {
                $campaign_response->refer_to = $request->input('child');
            }

            $campaign_response->save();

            DB::commit();

            return redirect()->route('our_campaigns.responses.index', $our_campaign)
                ->with('success', 'Response updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Database error: ' . $e->getMessage())->withInput();
        }
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Error: ' . $e->getMessage())->withInput();
    }
}




// public function admin_update(Request $request, OurCampaign $our_campaign, CampaignResponse $campaign_response)
// {
//     try {
//         if ($campaign_response->campaign_id !== $our_campaign->id) {
//             abort(404);
//         }

//         // Get ADMIN fields
//         $fields = $our_campaign->newFormFields->where('field_role', 'admin');

//         // Log incoming request data
//         Log::info('Incoming request data:', [
//             'request_all' => $request->all(),
//             'fields' => $fields->toArray()
//         ]);

//         if ($fields->isEmpty()) {
//             return redirect()
//                 ->back()
//                 ->with('error', 'No Admin fields found for this campaign.');
//         }

//         // Build validation rules for ADMIN fields
//         $rules = [];
//         foreach ($fields as $field) {
//             $key = $field->name;
//             $rules[$key] = $field->required ? 'required' : 'nullable';

//             if ($field->type === 'email') {
//                 $rules[$key] .= '|email';
//             } elseif ($field->type === 'number') {
//                 $rules[$key] .= '|numeric';
//             }
//         }

//         // Add validation rule for refer_to (if child is selected)
//         if ($request->has('child')) {
//             $rules['refer_to'] = 'nullable|integer';
//         }

//         // Validate the request
//         $validatedData = $request->validate($rules);

//         // Get existing submission data
//         $existingData = $campaign_response->submission_data ?? [];

//         // Merge the validated admin data directly into existing data
//         $updatedData = array_merge($existingData, $validatedData);

//         DB::beginTransaction();

//         try {
//             // Update submission data
//             $campaign_response->submission_data = $updatedData;

//             // Update refer_to if child is provided
//             if ($request->has('child') && $request->input('child')) {
//                 $campaign_response->refer_to = $request->input('child');
//             }

//             $campaign_response->save();

//             DB::commit();

//             return redirect()
//                 ->route('our_campaigns.responses.index', $our_campaign)
//                 ->with('success', 'Response updated successfully!');
//         } catch (\Exception $e) {
//             DB::rollBack();
//             Log::error('Database error:', [
//                 'message' => $e->getMessage(),
//                 'trace' => $e->getTraceAsString()
//             ]);

//             return redirect()
//                 ->back()
//                 ->with('error', 'Database error: ' . $e->getMessage())
//                 ->withInput();
//         }
//     } catch (\Exception $e) {
//         Log::error('General error:', [
//             'message' => $e->getMessage(),
//             'trace' => $e->getTraceAsString()
//         ]);

//         return redirect()
//             ->back()
//             ->with('error', 'Error: ' . $e->getMessage())
//             ->withInput();
//     }
// }



public function updateReferTo(Request $request)
{
    $request->validate([
        'refer_to' => 'required|integer',
        'campaign_response_id' => 'required|integer|exists:campaign_responses,id',
    ]);

    try {
        $campaignResponse = CampaignResponse::findOrFail($request->campaign_response_id);
        $campaignResponse->refer_to = $request->refer_to;
        $campaignResponse->save();

        return response()->json(['success' => true, 'message' => 'Refer To field updated successfully.']);
    } catch (\Exception $e) {
        Log::error('Error updating Refer To field:', ['error' => $e->getMessage()]);
        return response()->json(['success' => false, 'message' => 'Failed to update Refer To field.']);
    }
}




public function client_edit(OurCampaign $our_campaign, CampaignResponse $campaign_response)
{
    if ($campaign_response->campaign_id !== $our_campaign->id) {
        abort(404);
    }

    $allFields = $our_campaign->newFormFields->whereIn('field_role', ['client']); 
    $submittedData = $campaign_response->submission_data ?? [];

    $fieldsGroupedByRole = $allFields->groupBy('field_role');

    return view('campaign_responses.client_edit', compact('our_campaign', 'campaign_response', 'fieldsGroupedByRole', 'submittedData'));
}

public function client_update(Request $request, OurCampaign $our_campaign, CampaignResponse $campaign_response)
{
    try {
        if ($campaign_response->campaign_id !== $our_campaign->id) {
            abort(404);
        }

        // Get client fields
        $fields = $our_campaign->newFormFields->where('field_role', 'client');

        if ($fields->isEmpty()) {
            return redirect()
                ->back()
                ->with('error', 'No Client fields found for this campaign.');
        }

        // Build validation rules
        $rules = [];
        foreach ($fields as $field) {
            $key = 'client.' . $field->name;
            $rules[$key] = $field->required ? 'required' : 'nullable';

            if ($field->type === 'email') {
                $rules[$key] .= '|email';
            } elseif ($field->type === 'number') {
                $rules[$key] .= '|numeric';
            }
        }

        // Validate the request
        $validatedData = $request->validate($rules);
        $clientData = $validatedData['client'] ?? [];

        // Merge existing data with client data
        $existingData = $campaign_response->submission_data ?? [];
        $updatedData = array_merge($existingData, $clientData);

        DB::beginTransaction();
        $campaign_response->submission_data = $updatedData;
        $campaign_response->save();
        DB::commit();

        return redirect()
            ->route('our_campaigns.responses.index', $our_campaign)
            ->with('success', 'Client response updated successfully!');
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Client update error:', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);

        return redirect()
            ->back()
            ->with('error', 'Error: ' . $e->getMessage())
            ->withInput();
    }
}





    
    public function destroy(OurCampaign $our_campaign, CampaignResponse $campaign_response)
    {
        $campaign_response->delete();
        return redirect()->route('our_campaigns.responses.index', $our_campaign)->with('success', 'Response deleted!');
    }


    public function closer_edit(OurCampaign $our_campaign, CampaignResponse $campaign_response)
{
    if ($campaign_response->campaign_id !== $our_campaign->id) {
        abort(404);
    }

    $allFields = $our_campaign->newFormFields->whereIn('field_role', ['closer']); 
    $submittedData = $campaign_response->submission_data ?? [];

    $fieldsGroupedByRole = $allFields->groupBy('field_role');

    return view('campaign_responses.closer-edit', compact('our_campaign', 'campaign_response', 'fieldsGroupedByRole', 'submittedData'));
}

public function closer_update(Request $request, OurCampaign $our_campaign, CampaignResponse $campaign_response)
{
    try {
        if ($campaign_response->campaign_id !== $our_campaign->id) {
            abort(404);
        }

        // Get client fields
        $fields = $our_campaign->newFormFields->where('field_role', 'closer');

        if ($fields->isEmpty()) {
            return redirect()
                ->back()
                ->with('error', 'No CLoser fields found for this campaign.');
        }

        // Build validation rules
        $rules = [];
        foreach ($fields as $field) {
            $key = 'closer.' . $field->name;
            $rules[$key] = $field->required ? 'required' : 'nullable';

            if ($field->type === 'email') {
                $rules[$key] .= '|email';
            } elseif ($field->type === 'number') {
                $rules[$key] .= '|numeric';
            }
        }

        // Validate the request
        $validatedData = $request->validate($rules);
        $clientData = $validatedData['closer'] ?? [];

        // Merge existing data with client data
        $existingData = $campaign_response->submission_data ?? [];
        $updatedData = array_merge($existingData, $clientData);

        DB::beginTransaction();
        $campaign_response->submission_data = $updatedData;
        $campaign_response->save();
        DB::commit();

        return redirect()
            ->route('our_campaigns.responses.index', $our_campaign)
            ->with('success', 'CLoser response updated successfully!');
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Closer update error:', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);

        return redirect()
            ->back()
            ->with('error', 'Error: ' . $e->getMessage())
            ->withInput();
    }
}
}