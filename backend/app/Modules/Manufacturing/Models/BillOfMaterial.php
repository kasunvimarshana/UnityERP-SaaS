<?php

declare(strict_types=1);

namespace App\Modules\Manufacturing\Models;

use App\Core\Traits\Auditable;
use App\Core\Traits\HasUuid;
use App\Core\Traits\TenantScoped;
use App\Modules\Product\Models\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class BillOfMaterial extends Model
{
    use HasFactory, SoftDeletes, TenantScoped, HasUuid, Auditable;

    protected $fillable = [
        'tenant_id',
        'product_id',
        'bom_number',
        'name',
        'version',
        'status',
        'quantity',
        'unit_id',
        'production_time_minutes',
        'cost',
        'notes',
        'is_default',
        'valid_from',
        'valid_until',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'production_time_minutes' => 'integer',
        'cost' => 'decimal:2',
        'is_default' => 'boolean',
        'valid_from' => 'date',
        'valid_until' => 'date',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(BOMItem::class, 'bom_id');
    }
}
