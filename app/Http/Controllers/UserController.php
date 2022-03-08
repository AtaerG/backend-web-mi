<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Gate::denies('index', Auth::user())) {
            abort(403);
        }
        $users = User::get();
        return response()->json($users, 200);
    }



    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        if ($user->can('update', $user)) {
            return response()->json($user, 200);
        } else {
            return response()->json(['error'=>'No tiene permimsos para ver usuario'], 403);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        if ($user->can('update', $user)) {
            $user->name = $request->get('name');
            $user->surname = $request->get('surname');
            $user->password = bcrypt($request->get('password'));
            $user->save();
            return response()->json($user, 201);
        } else {
            return response()->json(['error'=>'No tiene permimsos para modificar usuario'], 403);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        if ($user->can('destroy', $user)) {
            $user->delete();
            return response()->json(null, 204);
        } else {
            return response()->json(['error'=> 'No tiene permisos para eliminar usuario'], 403);
        }
    }

    public function getOnlyAdminsIdForChatting(){
        $users = User::get();
        $names = $users->map->only(['name']);
        return response()->json($names, 200);
    }
}
