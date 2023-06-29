<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        
        \App\Models\User::factory()->create([
            'name' => 'Alex Dosse',
            'email' => 'alex.dosse@centify.com',
            'password' => Hash::make('centify'),
        ]);
    }
}
