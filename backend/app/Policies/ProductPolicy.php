<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Modules\Product\Models\Product;

class ProductPolicy
{
    /**
     * Determine whether the user can view any products.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view-products');
    }

    /**
     * Determine whether the user can view the product.
     */
    public function view(User $user, Product $product): bool
    {
        // Check permission and tenant isolation
        return $user->can('view-products') && $user->tenant_id === $product->tenant_id;
    }

    /**
     * Determine whether the user can create products.
     */
    public function create(User $user): bool
    {
        return $user->can('create-products');
    }

    /**
     * Determine whether the user can update the product.
     */
    public function update(User $user, Product $product): bool
    {
        // Check permission and tenant isolation
        return $user->can('edit-products') && $user->tenant_id === $product->tenant_id;
    }

    /**
     * Determine whether the user can delete the product.
     */
    public function delete(User $user, Product $product): bool
    {
        // Check permission and tenant isolation
        return $user->can('delete-products') && $user->tenant_id === $product->tenant_id;
    }

    /**
     * Determine whether the user can restore the product.
     */
    public function restore(User $user, Product $product): bool
    {
        // Check permission and tenant isolation
        return $user->can('edit-products') && $user->tenant_id === $product->tenant_id;
    }

    /**
     * Determine whether the user can permanently delete the product.
     */
    public function forceDelete(User $user, Product $product): bool
    {
        // Check permission and tenant isolation
        return $user->can('delete-products') && $user->tenant_id === $product->tenant_id;
    }
}
