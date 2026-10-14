<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\Supplier;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_seed_builds_a_coherent_catalog_for_query_examples(): void
    {
        $this->seed();

        $this->assertSame(20, User::query()->count());
        $this->assertSame(6, Category::query()->count());
        $this->assertSame(15, Supplier::query()->count());
        $this->assertSame(12, Tag::query()->count());
        $this->assertSame(120, Product::query()->count());
        $this->assertGreaterThanOrEqual(240, ProductReview::query()->count());
        $this->assertGreaterThanOrEqual(240, DB::table('product_supplier')->count());
        $this->assertGreaterThanOrEqual(132, DB::table('taggables')->count());
        $this->assertSame(120, Product::query()->has('suppliers', '>=', 2)->count());
        $this->assertSame(120, Product::query()->has('reviews', '>=', 2)->count());
        $this->assertSame(6, Category::query()->has('tags', '>=', 2)->count());
    }
}
