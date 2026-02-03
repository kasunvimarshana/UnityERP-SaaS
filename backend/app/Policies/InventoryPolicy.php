<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Modules\Inventory\Models\StockLedger;

class InventoryPolicy
{
    /**
     * Determine whether the user can view any inventory records.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view-inventory');
    }

    /**
     * Determine whether the user can view the inventory record.
     */
    public function view(User $user, StockLedger $stockLedger): bool
    {
        // Check permission and tenant isolation
        return $user->can('view-inventory') && $user->tenant_id === $stockLedger->tenant_id;
    }

    /**
     * Determine whether the user can perform stock in operations.
     */
    public function stockIn(User $user): bool
    {
        return $user->can('stock-in');
    }

    /**
     * Determine whether the user can perform stock out operations.
     */
    public function stockOut(User $user): bool
    {
        return $user->can('stock-out');
    }

    /**
     * Determine whether the user can perform stock adjustments.
     */
    public function stockAdjustment(User $user): bool
    {
        return $user->can('stock-adjustment');
    }

    /**
     * Determine whether the user can perform stock transfers.
     */
    public function stockTransfer(User $user): bool
    {
        return $user->can('stock-transfer');
    }

    /**
     * Determine whether the user can manage inventory.
     */
    public function manage(User $user): bool
    {
        return $user->can('manage-inventory');
    }
}
