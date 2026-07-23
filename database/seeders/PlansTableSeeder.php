<?php

namespace Database\Seeders;
use App\Models\Plan;
use Illuminate\Database\Seeder;

// addad this line by taraq Khan 
class PlansTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Plan::create(
            [
                'name' => 'J.Sons Plan',
                'price' => 0,
                'duration' => 'unlimited',
                'max_users' => 5000,
                'max_customers' => 5000,
                'max_venders' => 5000,
                'max_clients' => 5000,
                'crm' => 1,
                'hrm' => 1,
                'account' => 1,
                'project' => 1,
                'pos' => 1,
                'image'=>'J.Sons.png',
            ]
        );
    }
}
