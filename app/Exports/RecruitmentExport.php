<?php

namespace App\Exports;

use App\Models\Recruitment;
use Maatwebsite\Excel\Concerns\FromCollection;

class RecruitmentExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // return Recruitment::all();
        return Recruitment::latest()->take(500)->get();
    }
}
