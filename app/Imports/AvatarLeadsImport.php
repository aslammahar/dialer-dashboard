<?php

namespace App\Imports;

use App\Models\AvatarQALead;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AvatarLeadsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new AvatarQALead([
            'agent_email' => $row['agent_email'],
            'phone_number' => $row['phone_number'],
            'dialer_id' => $row['dialer_id'],
            'verifiers' => $row['verifiers'],
            'recording' => $row['recording'],
            'greetings' => $row['greetings'],
            'pitch_call_about' => $row['pitch_call_about'],
            'age' => $row['age'],
            'smoker' => $row['smoker'],
            'health1' => $row['health1'],
            'beneficiary' => $row['beneficiary'],
            'account' => $row['account'],
            'plan' => $row['plan'],
            'transfer_details' => $row['transfer_details'],
            'xfer_consent' => $row['xfer_consent'],
            'rebuttals' => $row['rebuttals'],
            'comments' => $row['comments'],
            'status' => $row['status'],
            'qa_person' => $row['qa_person'],
            'use_of_rebuttals' => $row['use_of_rebuttals'],
            'no_of_refusals' => $row['no_of_refusals'],
            'count' => $row['count'],
            'date_time' => $row['date_time'],
        ]);
    }
}
