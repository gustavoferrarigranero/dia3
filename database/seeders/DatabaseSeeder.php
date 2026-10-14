<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::factory()->count(20)->create();

        $this->call([
            CategorySeeder::class,
            SupplierSeeder::class,
            TagSeeder::class,
            ProductSeeder::class,
            ProductReviewSeeder::class,
        ]);
    }
}
