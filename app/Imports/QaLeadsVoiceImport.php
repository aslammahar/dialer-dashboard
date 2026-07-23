<?php

namespace App\Imports;

use App\Models\QaLeadsVoice;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class QaLeadsVoiceImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new QaLeadsVoice([
            'user_email' => $row['user_email'],
            'phone_number' => $row['phone_number'],
            'state' => $row['state'],
            'licenced_agent_name' => $row['licenced_agent_name'],
            'status' => $row['status'],
            'comments' => $row['comments'],
            'recordings' => $row['recordings'],
            'qa_person' => $row['qa_person'],
        ]);
    }
}



