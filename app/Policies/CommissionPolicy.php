<?php

namespace App\Policies;

use App\Models\Commission;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CommissionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view any commissions') || $user->can('view own commissions');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Commission $commission): bool
    {
        if ($user->can('view any commissions')) {
            return true;
        }

        return $user->can('view own commissions') && $commission->user_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create commission rules');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Commission $commission): bool
    {
        return $user->can('edit commission rules');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Commission $commission): bool
    {
        return $user->can('delete commission rules');
    }

    /**
     * Determine whether the user can export commissions.
     */
    public function export(User $user): bool
    {
        return $user->can('export commissions');
    }

    /**
     * Determine whether the user can view commission reports.
     */
    public function viewReports(User $user): bool
    {
        return $user->can('view any commissions');
    }

    /**
     * Determine whether the user can manage commission rules.
     */
    public function manageRules(User $user): bool
    {
        return $user->can('manage commission rules');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Commission $commission): bool
    {
        return $user->can('edit commission rules');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Commission $commission): bool
    {
        return $user->can('delete commission rules');
    }
}
