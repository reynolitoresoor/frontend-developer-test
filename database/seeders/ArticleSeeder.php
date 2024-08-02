<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
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
