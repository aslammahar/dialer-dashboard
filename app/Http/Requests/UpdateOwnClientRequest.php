<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOwnClientRequest extends FormRequest
{
    public function authorize()
    {
        // Authorization is handled in the controller
        return true;
    }

    public function rules()
    {
        return [
            'customer_full_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'alternate_phone_number' => 'nullable|string|max:20',
            'cx_email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:20',
            'gender' => 'nullable|string|max:10',
            'martial_status' => 'nullable|string|max:50',
            'age' => 'nullable|integer|min:0',
            'dob' => 'nullable|date',
            'social_security' => 'nullable|string|max:50',
            'smoker' => 'nullable|string|max:10',
            'health_condition' => 'nullable|string',
            'medication' => 'nullable|string',
            'hospital_name' => 'nullable|string|max:255',
            'hospital_address' => 'nullable|string|max:255',
            'physician_name' => 'nullable|string|max:255',
            'monthly_premium' => 'nullable|numeric|min:0',
            'customer_eligibility' => 'nullable|string|max:255',
            'beneficiary' => 'nullable|string|max:255',
            'beneficiary_relation' => 'nullable|string|max:100',
            'beneficiary_phone' => 'nullable|string|max:20',
            'beneficiary_dob' => 'nullable|date',
            'payor' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'bank_address' => 'nullable|string|max:255',
            'routing_number' => 'nullable|string|max:50',
            'bank_account_number' => 'nullable|string|max:50',
            'debit_card_direct_express_no' => 'nullable|string|max:50',
            'debit_card_direct_express_expiration' => 'nullable|string|max:10',
            'debit_card_direct_express_cvv' => 'nullable|string|max:10',
            'account_type' => 'nullable|string|max:50',
            'initial_draft_date' => 'nullable|date',
            'future_draft_date' => 'nullable|date',
            'remarks' => 'nullable|string',
            'status' => 'nullable|string|max:100',
            'clients_comment' => 'nullable|string',
            'recording_id' => 'nullable|string|max:255',
            'hippa_id' => 'nullable|string|max:255',
            'policy_id' => 'nullable|string|max:255',
            'recording_status' => 'nullable|string|max:100',
            'signature_type' => 'nullable|string|max:100',
            'call_id' => 'nullable|string|max:255',
            'carrier' => 'nullable|string|max:255',
            'client_name_2' => 'nullable|string|max:255',
        ];
    }
}
