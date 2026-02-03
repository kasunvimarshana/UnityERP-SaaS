<?php

declare(strict_types=1);

namespace App\Modules\Manufacturing\Models;

use App\Modules\Product\Models\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BOMItem extends Model
{
    use HasFactory;

    protected $table = 'bom_items';

    protected $fillable = [
        'bom_id',
        'product_id',
        'quantity',
        'unit_id',
        'scrap_percentage',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'scrap_percentage' => 'decimal:2',
    ];

    public function bom(): BelongsTo
    {
        return $this->belongsTo(BillOfMaterial::class, 'bom_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
