<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\Response;

class UserPolicy
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

    public function index(User $user){
        return ($user->role === 'admin') ?
        Response::allow()
        : Response::deny(Auth::user()->role.'No tiene permisos para listar usuarios');
    }
    public function show(User $user){
        return ($user->id === Auth::user()->id || Auth::user()->role === 'admin') ?
        Response::allow()
        : Response::deny(Auth::user()->id.'No tiene permisos para modificar commentario');
    }

    public function update(User $user){
        return ($user->id === Auth::user()->id || Auth::user()->role === 'admin') ?
        Response::allow()
        : Response::deny('No tiene permisos para modificar commentario');
    }

    public function destroy(User $user){
        return ($user->id ===  Auth::user()->id || Auth::user()->role === 'admin')?
        Response::allow()
        : Response::deny('No tiene permisos para eliminar commentario');
    }
}
