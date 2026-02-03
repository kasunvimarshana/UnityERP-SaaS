<?php

declare(strict_types=1);

namespace App\Modules\Manufacturing\Models;

use App\Core\Traits\Auditable;
use App\Core\Traits\HasUuid;
use App\Core\Traits\TenantScoped;
use App\Modules\Product\Models\Product;
use App\Modules\Tenant\Models\Branch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkOrder extends Model
{
    use HasFactory, SoftDeletes, TenantScoped, HasUuid, Auditable;

    protected $fillable = [
        'tenant_id',
        'branch_id',
        'product_id',
        'bom_id',
        'work_order_number',
        'status',
        'planned_quantity',
        'produced_quantity',
        'scrap_quantity',
        'unit_id',
        'planned_start_date',
        'planned_end_date',
        'actual_start_date',
        'actual_end_date',
        'priority',
        'notes',
    ];

    protected $casts = [
        'planned_quantity' => 'decimal:4',
        'produced_quantity' => 'decimal:4',
        'scrap_quantity' => 'decimal:4',
        'planned_start_date' => 'datetime',
        'planned_end_date' => 'datetime',
        'actual_start_date' => 'datetime',
        'actual_end_date' => 'datetime',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function bom(): BelongsTo
    {
        return $this->belongsTo(BillOfMaterial::class, 'bom_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
