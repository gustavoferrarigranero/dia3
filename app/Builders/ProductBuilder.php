<?php

namespace App\Builders;

use App\Models\ProductReview;
use Illuminate\Database\Eloquent\Builder;

final class ProductBuilder extends Builder
{
    public function filterName(string $name): static
    {
        return $this->when($name !== '', function($query, string $name) {
            $query->whereLike('name', "%{$name}%");
        });
    }

    public function filterDescription(string $description): static
    {
        return $this->whereLike('description', "%{$description}%");
    }

    public function filterCategory(array $categoryIds): static
    {
        return $this->whereIn('category_id', $categoryIds);
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

    public function addSelectLastReviewAt(): static
    {
        return $this->addSelect([
            'last_review_at' => ProductReview::query()
                ->select('created_at')
                ->whereColumn('product_id', 'products.id')
                ->whereNotNull('approved_at')
                ->latest()
                ->limit(1),
            ]);
    }
}