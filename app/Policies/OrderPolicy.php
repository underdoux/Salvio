<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrderPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view any orders') || $user->can('view own orders');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Order $order): bool
    {
        if ($user->can('view any orders')) {
            return true;
        }

        return $user->can('view own orders') && $order->user_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create orders');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Order $order): bool
    {
        if ($user->can('update any orders')) {
            return true;
        }

        return $user->can('update own orders') &&
               $order->user_id === $user->id &&
               $order->status === 'pending';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Order $order): bool
    {
        if ($user->can('delete any orders')) {
            return true;
        }

        return $user->can('delete own orders') &&
               $order->user_id === $user->id &&
               $order->status === 'pending';
    }

    /**
     * Determine whether the user can export orders.
     */
    public function export(User $user): bool
    {
        return $user->can('export orders');
    }

    /**
     * Determine whether the user can process orders.
     */
    public function process(User $user, Order $order): bool
    {
        if ($user->can('process any orders')) {
            return true;
        }

        return $user->can('process own orders') && $order->user_id === $user->id;
    }

    /**
     * Determine whether the user can cancel orders.
     */
    public function cancel(User $user, Order $order): bool
    {
        if ($user->can('cancel any orders')) {
            return true;
        }

        return $user->can('cancel own orders') &&
               $order->user_id === $user->id &&
               in_array($order->status, ['pending', 'processing']);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Order $order): bool
    {
        return $user->can('update any orders');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Order $order): bool
    {
        return $user->can('delete any orders');
    }

    /**
     * Determine whether the user can view order details.
     */
    public function viewDetails(User $user, Order $order): bool
    {
        if ($user->can('view any orders')) {
            return true;
        }

        return $user->can('view own orders') && $order->user_id === $user->id;
    }

    /**
     * Determine whether the user can view order history.
     */
    public function viewHistory(User $user, Order $order): bool
    {
        if ($user->can('view any orders')) {
            return true;
        }

        return $user->can('view own orders') && $order->user_id === $user->id;
    }

    /**
     * Determine whether the user can manage order items.
     */
    public function manageItems(User $user, Order $order): bool
    {
        if ($user->can('update any orders')) {
            return true;
        }

        return $user->can('update own orders') &&
               $order->user_id === $user->id &&
               $order->status === 'pending';
    }
}
