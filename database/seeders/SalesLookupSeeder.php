<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SalesTeam;
use App\Models\SalesCloser;
use App\Models\SalesClient;
use App\Models\SalesCarrier;

class SalesLookupSeeder extends Seeder
{
    public function run(): void
    {
        $teams = collect(['WFH Closing', 'Winning Edge', 'Sales Slayers', 'Jsons Team'])
            ->mapWithKeys(fn ($name) => [$name => SalesTeam::firstOrCreate(['name' => $name])->id]);

        // TODO: confirm/expand this list — pulled from the two screenshots.
        // team assignment here is a best guess; adjust in the sales_closers table as needed.
        $closers = [
            'Brian', 'Richard', 'Leo', 'Julia', 'Daniel', 'Ray', 'Ethan', 'Faith',
            'Alaska', 'Norman', 'Sarah', 'Jonathan', 'Bill', 'Shawn', 'Frank',
            'Sage', 'Helen', 'Andrew Parker', 'Jay Wilson', 'Kate', 'Albert',
            'Enrique', 'Lyla', 'Hazal',
        ];
        foreach ($closers as $name) {
            SalesCloser::firstOrCreate(['name' => $name]);
        }

        foreach (['D6', 'PM7', 'FCF', 'UL5'] as $name) {
            SalesClient::firstOrCreate(['name' => $name]);
        }

        foreach ([
            'CICA STANDARD', 'Aetna(CVS)', 'AFLAC', 'Securico Life',
            'CICA GI', 'GTL (Guaranteed)', 'AmAm', 'AIG',
        ] as $name) {
            SalesCarrier::firstOrCreate(['name' => $name]);
        }
    }
}