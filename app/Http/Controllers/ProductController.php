<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductReview;
use App\Models\ProductSupplier;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request): LengthAwarePaginator
    {
        // http://localhost:8000/api/products?search=phone

        $search = $request->string('search')->trim()->toString();
        $categoryId = $request->integer('category_id');
        $supplierId = $request->integer('supplier_id');
        $tag = $request->string('tag')->trim()->toString();
        $perPage = min(max($request->integer('per_page', 15), 1), 50);

        $includes = $request->string('include')
            ->explode(',')
            ->map(fn (string $include): string => trim($include))
            ->intersect(['category', 'tags', 'suppliers', 'reviews'])
            ->values()
            ->all();

        return Product::query()
            ->with($includes)
            ->where('is_active', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->where('stock', '>', 0)
            ->when(
                $search,
                fn (Builder $query): Builder => $query->where(
                    fn (Builder $searchQuery): Builder => $searchQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                )
            )
            ->when(
                $request->filled(['min_price', 'max_price']),
                fn (Builder $query): Builder => $query->whereBetween('price', [
                    $request->float('min_price'),
                    $request->float('max_price'),
                ])
            )
            ->when(
                $categoryId > 0,
                fn (Builder $query): Builder => $query->where('category_id', $categoryId)
            )
            ->when(
                $tag !== '',
                fn (Builder $query): Builder => $query->whereHas(
                    'tags',
                    fn (Builder $tagQuery): Builder => $tagQuery->where('slug', $tag)
                )
            )
            ->when(
                $supplierId > 0,
                fn (Builder $query): Builder => $query->whereHas(
                    'suppliers',
                    fn (Builder $supplierQuery): Builder => $supplierQuery
                        ->whereKey($supplierId)
                        ->where('product_supplier.is_active', true)
                )
            )
            ->withCount([
                'reviews as approved_reviews_count' => fn (Builder $query): Builder => $query->whereNotNull('approved_at'),
            ])
            ->withAvg([
                'reviews as average_rating' => fn (Builder $query): Builder => $query->whereNotNull('approved_at'),
            ], 'rating')
            ->addSelect([
                'last_review_at' => ProductReview::query()
                    ->select('created_at')
                    ->whereColumn('product_id', 'products.id')
                    ->whereNotNull('approved_at')
                    ->latest()
                    ->limit(1),
                'lowest_supplier_price' => ProductSupplier::query()
                    ->select('negotiated_price')
                    ->whereColumn('product_id', 'products.id')
                    ->where('is_active', true)
                    ->orderBy('negotiated_price')
                    ->limit(1),
            ])
            ->withCasts([
                'average_rating' => 'decimal:2',
                'last_review_at' => 'datetime',
                'lowest_supplier_price' => 'decimal:2',
            ])
            ->orderByDesc(
                ProductReview::query()
                    ->select('created_at')
                    ->whereColumn('product_id', 'products.id')
                    ->whereNotNull('approved_at')
                    ->latest()
                    ->limit(1)
            )
            ->orderByDesc('products.id')
            ->paginate($perPage)
            ->withQueryString();
    }
}
