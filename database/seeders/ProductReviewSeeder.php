<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductReview;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProductReviewSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        Product::query()->each(function (Product $product) use ($users): void {
            ProductReview::factory()
                ->count(fake()->numberBetween(2, 8))
                ->for($product)
                ->recycle($users)
                ->create();
        });
    }
}
