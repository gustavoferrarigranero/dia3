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
            ->where(function ($query) use ($search) {
                return $query->filterName($search)
                    ->filterDescription($search);
            })
            ->filterCategory($categoryId)
            ->filterSupplier($supplierId)
            ->filterTag($tag)
            ->addSelectLastReviewAt()
            ->addLowestSupplierPrice()
            ->orderByDesc(
                ProductReview::query()->addOrderByLastReviewAtForProduct()
            )
            ->orderByDesc('products.id')
            ->paginate($perPage)
            ->withQueryString();
    }
}
