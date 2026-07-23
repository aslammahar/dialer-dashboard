<?php

namespace App\Http\Controllers;

use App\Models\OurCampaign;
use App\Models\OurProject;
use App\Models\FormField;
use App\Models\CampaignResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Import DB
use Illuminate\Support\Facades\Log;

class OurCampaignController extends Controller
{
    public function index(OurProject $our_project)
    {
        $campaigns = $our_project->campaigns()->with('responses')->get(); // Eager load responses
        return view('our_campaigns.index', compact('our_project', 'campaigns'));
    }


    public function create(OurProject $our_project)
    {
        return view('our_campaigns.create', compact('our_project'));
    }

    public function store(Request $request, OurProject $our_project)
    {
        $our_campaign = $our_project->campaigns()->create($request->only('name', 'description'));
    
        if ($request->has('fields')) {
            foreach ($request->input('fields') as $key => $fieldData) {
                $fieldType = $fieldData['type'] ?? null;
                $fieldValue = $fieldData['value'] ?? null;
                $dateValue = null;
                $filePath = null;
                $options = null;
    
                if ($fieldType === 'date' && $fieldValue) {
                    try {
                        $dateValue = Carbon::parse($fieldValue);
                    } catch (\Exception $e) {
                        \Log::error("Date parsing error: " . $e->getMessage() . " Value: " . $fieldValue);
                        $dateValue = null;
                    }
                } elseif ($fieldType === 'file') {
                    if ($request->hasFile("fields.$key.value")) {
                        $file = $request->file("fields.$key.value");
                        $filePath = Storage::putFile('campaign_files', $file);
                    }
                } elseif ($fieldType === 'select' && isset($fieldData['options'])) {
                    $options = array_map('trim', explode("\n", $fieldData['options']));
                    $options = implode(",", $options);
                }
    
                // Convert show_to array to comma-separated string
                $showTo = isset($fieldData['show_to']) ? implode(',', $fieldData['show_to']) : null;
    
                $our_campaign->newFormFields()->create([
                    'label' => $fieldData['label'] ?? null,
                    'name' => $fieldData['name'] ?? null,
                    'type' => $fieldType,
                    'options' => $options,
                    'field_role' => $fieldData['role'] ?? null,
                    'required' => $fieldData['required'] ?? false,
                    'value' => $fieldValue,
                    'date_value' => $dateValue,
                    'file_path' => $filePath,
                    'show_to' => $showTo, // Add the new field
                ]);
            }
        }
    
        return redirect()->route('our_projects.our_campaigns.index', $our_project)
            ->with('success', 'Campaign and form fields created successfully.');
    }
    
  

    public function edit(OurProject $our_project, OurCampaign $our_campaign)
    {
        return view('our_campaigns.edit', compact('our_project', 'our_campaign'));
    }

    public function update(Request $request, OurProject $our_project, OurCampaign $our_campaign)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $our_campaign->update($request->all());

        return redirect()->route('our_projects.our_campaigns.index', $our_project)->with('success', 'Campaign updated successfully.');
    }


    public function destroy(OurProject $our_project, OurCampaign $our_campaign)
{
    try {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $our_campaign->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        return redirect()->route('our_projects.our_campaigns.index', $our_project)->with('success', 'Campaign deleted successfully.');
    } catch (\Exception $e) {
        Log::error($e); // Log the full exception for debugging

        // Get the specific error message (more user-friendly)
        $errorMessage = $e->getMessage();

        // More robust error handling: check for specific exception types
        if ($e instanceof \Illuminate\Database\QueryException) {
            // Database-specific error (e.g., foreign key constraint)
            $errorCode = $e->getCode();
            if ($errorCode == 1451) { // MySQL specific error code for foreign key constraint
                $errorMessage = "Cannot delete this campaign because it has associated responses. Delete the responses first.";
            }
            // Add more specific error code handling if needed
        }
        return redirect()->route('our_projects.our_campaigns.index', $our_project)->with('error', $errorMessage);
    }
}

    public function showForm(OurCampaign $our_campaign)
    {
        $formFields = $our_campaign->newFormFields; // Use the correct relationship name
       
        return view('our_campaigns.form', compact('our_campaign', 'formFields'));
    }

    public function showQaForm(OurProject $our_project, OurCampaign $our_campaign)
    {
        $formFields = $our_campaign->newFormFields; // Get all form fields for the campaign
        $campaigns = $our_project->campaigns()->with('responses')->get(); // Eager load responses


        return view('our_campaigns.qa-form', compact('our_project', 'our_campaign', 'formFields'));
    }


    public function submitForm(Request $request, OurCampaign $our_campaign)
    {
        $validatedData = $request->validate([]); // Add validation rules as needed
        $submissionData = $request->except('_token');
    
        CampaignResponse::create([
            'campaign_id' => $our_campaign->id,
            'submission_data' => $submissionData // Pass the array directly
        ]);
    
        return redirect()->back()->with('success', 'Form submitted successfully!');
    }

    public function storeFields(Request $request, OurCampaign $our_campaign)
    {
        $our_campaign = $our_project->campaigns()->create($request->only('name', 'description'));

        if ($request->has('fields')) {
            foreach ($request->input('fields') as $key => $fieldData) {
                $fieldType = $fieldData['type'] ?? null;
                $fieldValue = $fieldData['value'] ?? null;
                $dateValue = null;
                $filePath = null;

                if ($fieldType === 'date' && $fieldValue) {
                    try {
                        $dateValue = Carbon::parse($fieldValue);
                    } catch (\Exception $e) {
                        // Log the error or handle it as needed, but DON'T stop execution
                        \Log::error("Date parsing error: " . $e->getMessage() . " Value: " . $fieldValue);
                        $dateValue = null; // Set to null to avoid database errors
                    }
                } elseif ($fieldType === 'file') {
                    if ($request->hasFile("fields.$key.value")) {
                        $file = $request->file("fields.$key.value");
                        $filePath = Storage::putFile('campaign_files', $file);
                    }
                }

                $our_campaign->newFormFields()->create([
                    'label' => $fieldData['label'] ?? null,
                    'name' => $fieldData['name'] ?? null,
                    'type' => $fieldType,
                    'options' => $fieldData['options'] ?? null,
                    'role' => $fieldData['role'] ?? null,
                    'field_role' => $fieldData['role'] ?? null,
                    'required' => $fieldData['required'] ?? false,
                    'value' => $fieldValue,
                    'date_value' => $dateValue,
                    'file_path' => $filePath,
                ]);
            }

            dd($our_campaign);
        }

        return redirect()->route('our_projects.our_campaigns.index', $our_project)->with('success', 'Campaign and form fields created successfully.');
    }

public function showWithFields(OurCampaign $our_campaign)
{
    $formFields = $our_campaign->newFormFields; // Use the correct relationship name
    return view('our_campaigns.show-with-fields', compact('our_campaign', 'formFields'));
}
}