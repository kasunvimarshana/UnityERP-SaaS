<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'tenant_id' => $this->tenant_id,
            'name' => $this->name,
            'slug' => $this->slug,
            'sku' => $this->sku,
            'type' => $this->type,
            'description' => $this->description,
            'category_id' => $this->category_id,
            'unit_of_measure_id' => $this->unit_of_measure_id,
            'buying_price' => $this->buying_price,
            'selling_price' => $this->selling_price,
            'discount_type' => $this->discount_type,
            'discount_value' => $this->discount_value,
            'tax_rate_id' => $this->tax_rate_id,
            'profit_margin' => $this->profit_margin,
            'profit_margin_type' => $this->profit_margin_type,
            'min_stock_level' => $this->min_stock_level,
            'max_stock_level' => $this->max_stock_level,
            'reorder_level' => $this->reorder_level,
            'is_active' => $this->is_active,
            'has_variants' => $this->has_variants,
            'is_serialized' => $this->is_serialized,
            'is_batch_tracked' => $this->is_batch_tracked,
            'has_expiry' => $this->has_expiry,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
            
            // Related data (when loaded)
            'category' => $this->whenLoaded('category', function () {
                return [
                    'id' => $this->category->id,
                    'name' => $this->category->name,
                ];
            }),
            'unit_of_measure' => $this->whenLoaded('unitOfMeasure', function () {
                return [
                    'id' => $this->unitOfMeasure->id,
                    'name' => $this->unitOfMeasure->name,
                    'abbreviation' => $this->unitOfMeasure->abbreviation,
                ];
            }),
            'tax_rate' => $this->whenLoaded('taxRate', function () {
                return [
                    'id' => $this->taxRate->id,
                    'name' => $this->taxRate->name,
                    'rate' => $this->taxRate->rate,
                ];
            }),
            'variants' => $this->whenLoaded('variants', function () {
                return $this->variants->map(function ($variant) {
                    return [
                        'id' => $variant->id,
                        'name' => $variant->name,
                        'sku' => $variant->sku,
                        'selling_price' => $variant->selling_price,
                    ];
                });
            }),
        ];
    }
}
