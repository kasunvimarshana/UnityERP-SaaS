<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the user can view any users.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view-users');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        // Check permission and tenant isolation
        return $user->can('view-users') && $user->tenant_id === $model->tenant_id;
    }

    /**
     * Determine whether the user can create users.
     */
    public function create(User $user): bool
    {
        return $user->can('create-users');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        // Check permission and tenant isolation
        // Super admins can edit anyone, others can only edit in their tenant
        if ($user->hasRole('super-admin')) {
            return true;
        }
        
        return $user->can('edit-users') && $user->tenant_id === $model->tenant_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        // Cannot delete yourself
        if ($user->id === $model->id) {
            return false;
        }
        
        // Check permission and tenant isolation
        if ($user->hasRole('super-admin')) {
            return true;
        }
        
        return $user->can('delete-users') && $user->tenant_id === $model->tenant_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        return $user->can('edit-users') && $user->tenant_id === $model->tenant_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        // Cannot delete yourself
        if ($user->id === $model->id) {
            return false;
        }
        
        return $user->can('delete-users') && $user->tenant_id === $model->tenant_id;
    }
}
