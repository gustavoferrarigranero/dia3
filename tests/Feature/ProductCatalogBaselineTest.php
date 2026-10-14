<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class ProductCatalogBaselineTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_products_remain_queryable_before_the_custom_builder_lesson(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->for($category)->create([
            'sku' => 'LESSON-START',
        ]);

        $queriedProduct = Product::query()
            ->whereBelongsTo($category)
            ->where('sku', 'LESSON-START')
            ->firstOrFail();

        $this->assertTrue($product->is($queriedProduct));
    }
}
