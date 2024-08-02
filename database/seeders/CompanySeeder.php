<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;


class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Company::factory()->create([
            'logo' => 'uploads/logo.png',
            'name' => 'archintel.com',
            'status' => 'Active',
        ]);

        \App\Models\Company::factory()->create([
            'logo' => 'uploads/logo.png',
            'name' => 'executivemosaic.com',
            'status' => 'Active',
        ]);
    }
}
