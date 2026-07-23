<?php

namespace App\Http\Requests;

use App\Models\UserDetail;
use Illuminate\Foundation\Http\FormRequest;

class UserDetailRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $user = auth()->user();
        $userDetail = $user ? UserDetail::where('user_id', $user->id)->first() : null;
        $userDetailId = $userDetail ? $userDetail->id : null;
        
        $rules = [
            'full_name' => 'required|string|max:255',
            'father_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'cnic_number' => 'required|string|max:20',
            'address' => 'required|string',
            'date_of_birth' => 'required|date',
            'date_of_joining' => 'required|date',
            'emergency_phone' => 'required|string|max:20',
            'city' => 'required|string|max:100',
            'designation' => 'required|string|max:100',
            'work_from' => 'required|string|max:100',
            'employee_id' => 'required|string|max:50|unique:user_details,employee_id,' . $userDetailId,
            'bank_name.0' => 'required|string|max:255',
            'account_title.0' => 'required|string|max:255',
            'account_number.0' => 'required|string|max:50',
            'bank_cnic_number.0' => 'required|string|max:20',
        ];

        // File validation rules from config
        $fileRules = 'image|mimes:' . config('variables.attachments.mimes') . '|max:' . config('variables.attachments.max_size');
        
        if (!$userDetail) {
            // First time creation - files are required
            $rules['cnic_front'] = 'required|' . $fileRules;
            $rules['cnic_back'] = 'required|' . $fileRules;
        } else {
            // Update - files are optional
            $rules['cnic_front'] = 'nullable|' . $fileRules;
            $rules['cnic_back'] = 'nullable|' . $fileRules;
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'cnic_front.required' => 'CNIC front image is required',
            'cnic_back.required' => 'CNIC back image is required',
            'cnic_front.mimes' => 'CNIC front must be a file of type: ' . config('variables.attachments.mimes'),
            'cnic_back.mimes' => 'CNIC back must be a file of type: ' . config('variables.attachments.mimes'),
            'cnic_front.max' => 'CNIC front may not be greater than ' . (config('variables.attachments.max_size') / 1024) . ' MB',
            'cnic_back.max' => 'CNIC back may not be greater than ' . (config('variables.attachments.max_size') / 1024) . ' MB',
        ];
    }

    /**
     * Get the validated data from the request.
     */
    public function getValidatedData()
    {
        $validated = $this->validated();
        
        return [
            'full_name' => $validated['full_name'],
            'father_name' => $validated['father_name'],
            'pseudo_name' => $this->input('pseudo_name', ''),
            'phone' => $validated['phone'],
            'email' => $validated['email'],
            'team_leader' => $this->input('team_leader', ''),
            'cnic_number' => $validated['cnic_number'],
            'address' => $validated['address'],
            'date_of_birth' => $validated['date_of_birth'],
            'date_of_joining' => $validated['date_of_joining'],
            'source_of_joining' => $this->input('source_of_joining', ''),
            'emergency_phone' => $validated['emergency_phone'],
            'city' => $validated['city'],
            'designation' => $validated['designation'],
            'work_from' => $validated['work_from'],
            'employee_id' => $validated['employee_id'],
        ];
    }

    /**
     * Get bank details from validated data
     */
    public function getBankDetails()
    {
        $validated = $this->validated();
        
        return [
            'bank_names' => $validated['bank_name'] ?? [],
            'account_titles' => $validated['account_title'] ?? [],
            'account_numbers' => $validated['account_number'] ?? [],
            'bank_cnic_numbers' => $validated['bank_cnic_number'] ?? [],
        ];
    }
}