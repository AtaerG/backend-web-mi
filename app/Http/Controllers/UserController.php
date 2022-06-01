<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\Order;
use \Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\UserRequest;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Gate::allows('isAdmin')) {
            try {
                $users = User::get();
                return response()->json($users, 200);
            } catch (\Exception $e) {
                return response()->json(['error' => 'Error al obtener los usuarios'], 401);
            }
        } else {
            return response()->json(['error' => 'No tiene permisos'], 401);
        }
    }

    public function getAdmins()
    {
        try {
            $admins = DB::select("SELECT id, name FROM users WHERE role = 'admin'");
            return response()->json($admins, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener los administradores'], 401);
        }
    }



    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        if ($user->can('show', $user)) {
            try {
                return response()->json($user, 200);
            } catch (\Exception $e) {
                return response()->json(['error' => 'Error al obtener el usuario'], 401);
            }
        } else {
            return response()->json(['error' => 'No tiene permimsos'], 403);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(UserRequest $request, User $user)
    {
        if ($user->can('update', $user)) {
            try {
                $user->name = $request->get('name');
                $user->surname = $request->get('surname');
                $user->email = $request->get('email');
                $user->role = $request->get('role');
                $user->save();
                return response()->json($user, 201);
            } catch (\Exception $e) {
                return response()->json(['error' => 'Error al actualizar el usuario'], 401);
            }
        } else {
            return response()->json(['error' => 'No tiene permimsos'], 403);
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
            try {
                $value = true;
                $orders = DB::select("SELECT id FROM orders WHERE user_id = ?", [$user->id]);
                foreach ($orders as $order) {
                    $order = Order::find($order->id);
                    if ($order->status == 'pagado') {
                        $value = false;
                        break;
                    }
                }

                if (!$value) {
                    return response()->json(['error' => 'No se puede eliminar el usuario'], 404);
                }
                if ($user->role == 'admin') {
                    DB::delete("DELETE FROM appointments WHERE admin_id = ?", [$user->id]);
                    $user->delete();
                    return response()->json(null, 204);
                } else {
                    DB::delete("DELETE FROM comments WHERE user_id = ?", [$user->id]);
                    DB::delete("DELETE FROM order_product WHERE order_id IN (SELECT id FROM orders WHERE user_id = ?)", [$user->id]);
                    DB::delete("DELETE FROM orders WHERE user_id = ?", [$user->id]);
                    DB::delete("DELETE FROM appointments WHERE user_id = ?", [$user->id]);
                    $user->delete();
                    return response()->json(null, 204);
                }
            } catch (\Exception $e) {
                return response()->json(['error' => 'Error al eliminar el usuario'], 401);
            }
        } else {
            return response()->json(['error' => 'No tiene permisos'], 403);
        }
    }

    public function getOnlyAdminsIdForChatting()
    {
        try {
            $users = User::get();
            $names = $users->map->only(['name']);
            return response()->json($names, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener los administradores'], 401);
        }
    }
}
