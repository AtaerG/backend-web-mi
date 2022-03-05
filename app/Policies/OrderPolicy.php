<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\Order;

class OrderPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function update(User $user, Order $order)
    {
        return $user->id === $order->user_id || $user->role() === 'admin';
    }

    public function destroy(User $user, Order $order)
    {
        return $user->id === $order->user_id || $user->role() === 'admin';
    }


    public function update(User $user, Order $order)
    {
        return $user->id === $order->user_id || $user->role() === 'admin';
    }

    public function destroy(User $user, Order $order)
    {
        return $user->id === $order->user_id || $user->role() === 'admin';
    }
}
