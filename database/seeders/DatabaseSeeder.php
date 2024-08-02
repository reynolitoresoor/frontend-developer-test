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
        
        /* User seeder */
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

        /* Company seeder */
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

        /* Article seeder */
        \App\Models\Article::factory()->create([
            'image' => 'uploads/sample.jpg',
            'title' => 'test writer article',
            'link' => 'http://test-link.com',
            'date' => '2024-08-01',
            'content' => 'test content',
            'status' => 'active',
            'writer' => 2,
            'editor' => 1,
            'company' => 1
        ]);
        \App\Models\Article::factory()->create([
            'image' => 'uploads/sample.jpg',
            'title' => 'test writer article 2',
            'link' => 'http://test-link.com',
            'date' => '2024-08-01',
            'content' => 'test content',
            'status' => 'active',
            'writer' => 2,
            'editor' => 1,
            'company' => 2
        ]);
    }
}
