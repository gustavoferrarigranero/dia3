<?php

namespace App\Models;

use App\Builders\ProductSupplierBuilder;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ProductSupplier extends Pivot
{
    protected $table = 'product_supplier';

    protected function casts(): array
    {
        return [
            'negotiated_price' => 'decimal:2',
            'lead_time_days' => 'integer',
            'is_preferred' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function newEloquentBuilder($query): ProductSupplierBuilder
    {
        return new ProductSupplierBuilder($query);
    }
}
