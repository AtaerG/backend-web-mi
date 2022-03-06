<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\Order;
use Illuminate\Auth\Access\Response;

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

    public function index(User $user, Order $order)
    {
        return ($user->id === $order->user_id || $user->role()->first()->role === 'admin') ?
        Response::allow()
        : Response::deny('No tiene permisos para ver pedidos');
    }

    public function show(User $user, Order $order)
    {
        return ($user->id === $order->user_id || $user->role()->first()->role === 'admin') ?
        Response::allow()
        : Response::deny('No tiene permisos para ver pedido');
    }


    public function update(User $user, Order $order)
    {
        return ($user->id === $order->user_id || $user->role()->first()->role === 'admin') ?
        Response::allow()
        : Response::deny('No tiene permisos para modificar pedido');
    }

    public function destroy(User $user, Order $order)
    {
        return ($user->id === $order->user_id || $user->role()->first()->role === 'admin') ?
        Response::allow()
        : Response::deny('No tiene permisos para eliminar pedido');
    }
}
