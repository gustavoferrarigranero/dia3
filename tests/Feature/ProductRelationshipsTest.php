<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\ProductSupplier;
use App\Models\Supplier;
use App\Models\Tag;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class ProductRelationshipsTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_category_exposes_reviews_through_its_products(): void
    {
        $category = Category::factory()->create();
        $otherCategory = Category::factory()->create();
        $firstProduct = Product::factory()->for($category)->create();
        $secondProduct = Product::factory()->for($category)->create();
        $otherProduct = Product::factory()->for($otherCategory)->create();
        $firstReview = ProductReview::factory()->for($firstProduct)->create();
        $secondReview = ProductReview::factory()->for($secondProduct)->create();
        ProductReview::factory()->for($otherProduct)->create();

        $reviewIds = $category->reviews()->pluck('product_reviews.id')->all();

        $this->assertEqualsCanonicalizing(
            [$firstReview->getKey(), $secondReview->getKey()],
            $reviewIds
        );
    }

    public function test_products_and_categories_share_polymorphic_tags(): void
    {
        $product = Product::factory()->create();
        $category = Category::factory()->create();
        $tag = Tag::factory()->create();
        $product->tags()->attach($tag);
        $category->tags()->attach($tag);

        $productIds = $tag->products()->pluck('products.id')->all();
        $categoryIds = $tag->categories()->pluck('categories.id')->all();

        $this->assertSame([$product->getKey()], $productIds);
        $this->assertSame([$category->getKey()], $categoryIds);
    }

    public function test_supplier_offer_uses_the_custom_pivot_and_casts_its_attributes(): void
    {
        $product = Product::factory()->create();
        $supplier = Supplier::factory()->create();
        $product->suppliers()->attach($supplier, [
            'supplier_sku' => 'SUP-1000',
            'negotiated_price' => 199.90,
            'lead_time_days' => 7,
            'is_preferred' => true,
            'is_active' => true,
        ]);

        $offer = $product->suppliers()->firstOrFail()->offer;

        $this->assertInstanceOf(ProductSupplier::class, $offer);
        $this->assertSame('199.90', $offer->negotiated_price);
        $this->assertSame(7, $offer->lead_time_days);
        $this->assertTrue($offer->is_preferred);
        $this->assertTrue($offer->is_active);
    }
}
