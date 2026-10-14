<?php

namespace App\Builders;

use App\Models\ProductReview;
use App\Models\ProductSupplier;
use Illuminate\Database\Eloquent\Builder;

final class ProductBuilder extends Builder
{
    public function filterName(string $name): static
    {
        return $this->when($name !== '', function($query) use($name) {
            $query->orWhereLike('name', "%{$name}%");
        });
    }

    public function filterDescription(string $description): static
    {
        return $this->when($description !== '', function($query) use($description) {
            $query->orWhereLike('description', "%{$description}%");
        });
    }

    public function filterCategory(int $categoryId): static
    {
        return $this->when($categoryId > 0, function($query) use($categoryId) {
            $query->where('category_id', $categoryId);
        });
    }

    public function filterSupplier(int $supplierId): static
    {
        return $this->when(
            $supplierId > 0,
            fn (Builder $query): Builder => $query->whereHas(
                'suppliers',
                fn (Builder $supplierQuery): Builder => $supplierQuery
                    ->whereKey($supplierId)
                    ->where('product_supplier.is_active', true)
            )
        );
    }

    public function filterTag(string $tag): static
    {
        return $this->when(
            $tag !== '',
            fn (Builder $query): Builder => $query->whereHas(
                'tags',
                fn (Builder $tagQuery): Builder => $tagQuery->where('name', $tag)
        ));
    }

    public function addSelectLastReviewAt(): static
    {
        return $this->addSelect([
            'last_review_at' => ProductReview::query()
                ->addLastReviewAtForProduct()
                ->limit(1),
            ]);
    }

    public function addLowestSupplierPrice(): static
    {
        return $this->addSelect([
            'lowest_supplier_price' => ProductSupplier::query()
                ->addLowestSupplierPriceForProduct()
                ->limit(1),
            ]);
    }
}