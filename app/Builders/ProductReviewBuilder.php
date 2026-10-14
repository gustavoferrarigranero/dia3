<?php

namespace App\Builders;

use Illuminate\Database\Eloquent\Builder;

final class ProductReviewBuilder extends Builder
{
    public function addLastReviewAtForProduct(): static
    {
        return $this->select('created_at')
            ->whereColumn('product_id', 'products.id')
            ->whereNotNull('approved_at')
            ->latest();
    }

    public function addOrderByLastReviewAtForProduct(): static
    {
        return $this->select('created_at')
            ->whereColumn('product_id', 'products.id')
            ->whereNotNull('approved_at')
            ->latest()
            ->limit(1);
    }
}