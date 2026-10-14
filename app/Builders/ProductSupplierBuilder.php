<?php

namespace App\Builders;

use App\Models\ProductReview;
use App\Models\ProductSupplier;
use Illuminate\Database\Eloquent\Builder;

final class ProductSupplierBuilder extends Builder
{
    public function addLowestSupplierPriceForProduct(): static
    {
        return $this->select('negotiated_price')
            ->whereColumn('product_id', 'products.id')
            ->where('is_active', true)
            ->orderBy('negotiated_price');
    }
}