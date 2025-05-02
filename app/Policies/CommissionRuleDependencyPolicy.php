<?php

namespace App\Policies;

use App\Models\CommissionRule;
use App\Models\CommissionRuleDependency;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CommissionRuleDependencyPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any dependencies.
     */
    public function viewAny(User $user, CommissionRule $rule): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can view the dependency graph.
     */
    public function viewGraph(User $user, CommissionRule $rule): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can view dependency analysis.
     */
    public function viewAnalysis(User $user, CommissionRule $rule): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can create dependencies.
     */
    public function create(User $user, CommissionRule $rule): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can delete dependencies.
     */
    public function delete(User $user, CommissionRule $rule, CommissionRuleDependency $dependency): bool
    {
        // Only admin can delete dependencies and only if they belong to the rule
        return $user->role === 'admin' && $dependency->commission_rule_id === $rule->getKey();
    }

    /**
     * Determine whether the user can validate dependencies.
     */
    public function validate(User $user, CommissionRule $rule): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can manage dependencies in bulk.
     */
    public function manageBulk(User $user): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can detect conflicts.
     */
    public function detectConflicts(User $user): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can resolve conflicts.
     */
    public function resolveConflicts(User $user): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can update dependency settings.
     */
    public function updateSettings(User $user): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can export dependency data.
     */
    public function export(User $user): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can import dependency data.
     */
    public function import(User $user): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can view dependency history.
     */
    public function viewHistory(User $user, CommissionRule $rule): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can restore dependency versions.
     */
    public function restoreVersion(User $user, CommissionRule $rule): bool
    {
        return $user->role === 'admin';
    }
}
