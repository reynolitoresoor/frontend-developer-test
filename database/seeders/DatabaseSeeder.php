<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        \App\Models\User::factory()->create([
            'firstname' => 'editor',
            'lastname' => 'editor',
            'type' => 'Editor',
            'status' => 'Active',
            'email' => 'editor@gmail.com',
            'password' => bcrypt('editor12345')
        ]);

        \App\Models\User::factory()->create([
            'firstname' => 'writer',
            'lastname' => 'writer',
            'type' => 'Writer',
            'status' => 'Active',
            'email' => 'writer@gmail.com',
            'password' => bcrypt('writer12345')
        ]);
    }
}
