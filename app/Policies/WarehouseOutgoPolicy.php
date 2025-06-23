<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WarehouseOutgo;
use Illuminate\Auth\Access\HandlesAuthorization;

class WarehouseOutgoPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_warehouse::outgo');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, WarehouseOutgo $warehouseOutgo): bool
    {
        return $user->can('view_warehouse::outgo');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_warehouse::outgo');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, WarehouseOutgo $warehouseOutgo): bool
    {
        return $user->can('update_warehouse::outgo');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, WarehouseOutgo $warehouseOutgo): bool
    {
        return $user->can('delete_warehouse::outgo');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_warehouse::outgo');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, WarehouseOutgo $warehouseOutgo): bool
    {
        return $user->can('force_delete_warehouse::outgo');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_warehouse::outgo');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, WarehouseOutgo $warehouseOutgo): bool
    {
        return $user->can('restore_warehouse::outgo');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_warehouse::outgo');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, WarehouseOutgo $warehouseOutgo): bool
    {
        return $user->can('replicate_warehouse::outgo');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_warehouse::outgo');
    }
}
