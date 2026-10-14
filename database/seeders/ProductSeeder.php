<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::all();
        $suppliers = Supplier::all();
        $tags = Tag::all();

        Product::factory()
            ->count(120)
            ->recycle($categories)
            ->create()
            ->each(function (Product $product) use ($suppliers, $tags): void {
                $selectedSuppliers = $suppliers->random(fake()->numberBetween(2, 5));
                $preferredSupplierId = $selectedSuppliers->random()->getKey();

                $offers = $selectedSuppliers->mapWithKeys(
                    fn (Supplier $supplier): array => [
                        $supplier->getKey() => [
                            'supplier_sku' => fake()->unique()->bothify('SUP-####-???'),
                            'negotiated_price' => round((float) $product->price * fake()->randomFloat(2, 0.55, 0.92), 2),
                            'lead_time_days' => fake()->numberBetween(1, 30),
                            'is_preferred' => $supplier->getKey() === $preferredSupplierId,
                            'is_active' => $supplier->is_active && fake()->boolean(90),
                        ],
                    ]
                );

                $product->suppliers()->attach($offers->all());
                $product->tags()->attach($this->randomTagIds($tags));
            });
    }

    private function randomTagIds(Collection $tags): array
    {
        return $tags->random(fake()->numberBetween(1, 4))->pluck('id')->all();
    }
}
