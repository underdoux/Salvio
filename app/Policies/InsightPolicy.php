<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class InsightPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view insights.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view insights');
    }

    /**
     * Determine whether the user can view financial insights.
     */
    public function viewFinancial(User $user): bool
    {
        return $user->can('view financial insights');
    }

    /**
     * Determine whether the user can view sales insights.
     */
    public function viewSales(User $user): bool
    {
        return $user->can('view sales insights');
    }

    /**
     * Determine whether the user can view product insights.
     */
    public function viewProducts(User $user): bool
    {
        return $user->can('view product insights');
    }

    /**
     * Determine whether the user can export insights.
     */
    public function export(User $user): bool
    {
        return $user->can('export insights');
    }

    /**
     * Determine whether the user can schedule reports.
     */
    public function scheduleReports(User $user): bool
    {
        return $user->can('schedule reports');
    }

    /**
     * Determine whether the user can view customer analytics.
     */
    public function viewCustomerAnalytics(User $user): bool
    {
        return $user->can('view customer analytics');
    }

    /**
     * Determine whether the user can view commission analytics.
     */
    public function viewCommissionAnalytics(User $user): bool
    {
        return $user->can('view commission analytics');
    }

    /**
     * Determine whether the user can customize insights.
     */
    public function customize(User $user): bool
    {
        return $user->can('customize insights');
    }

    /**
     * Determine whether the user can share insights.
     */
    public function share(User $user): bool
    {
        return $user->can('share insights');
    }

    /**
     * Determine whether the user can create custom reports.
     */
    public function createCustomReports(User $user): bool
    {
        return $user->can('create custom reports');
    }

    /**
     * Determine whether the user can manage report schedules.
     */
    public function manageSchedules(User $user): bool
    {
        return $user->can('manage report schedules');
    }
}
