<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('staging') || app()->environment('local')) {
            $this->call(TruncateTables::class);
        }

        $this->call(TestDataSeeder::class);
    }
}
