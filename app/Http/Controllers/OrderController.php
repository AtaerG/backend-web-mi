<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Mail\OrderCreatedMail;
use App\Mail\ValorationMail;
use App\Mail\OrderDeletedMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\OrderRequest;
use App\Http\Requests\OrdersUserRequest;
use App\Http\Requests\OrderStatusRequest;
use App\Http\Requests\OrderUpdateRequest;
use App\Http\Requests\OrderValorationRequest;
use App\Mail\OrderChangedMail;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $user_orders = [];
            $orders = Order::get();
            foreach ($orders as $order) {
                if (Gate::denies('show', $order)) {
                    continue;
                } else {
                    array_push($user_orders, $order);
                }
            }
            return response()->json($user_orders, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener los pedidos'], 401);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(OrderRequest $request)
    {
        if (Gate::denies('isAdmin')) {
            try {
                $order  = new Order();
                $order->total_price = $request->get('total_price');
                $order->direction = $request->get('direction');
                $order->post_code = $request->get('post_code');
                $order->status = $request->get('status');
                $order->city = $request->get('city');
                $order->state = $request->get('state');
                $order->country = $request->get('country');
                $order->user()->associate(Auth::user()->id);
                $order->save();
                foreach (json_decode(json_decode($request->get('products'))) as $product) {
                    DB::table('order_product')->insert(
                        ['product_id' => $product->product->id, 'amount' => $product->amount, 'order_id' => $order->id]
                    );
                }
                $user = $order->user()->first();
            } catch (\Exception $e) {
                return response()->json(['error' => 'Error al crear el pedido'], 401);
            }

            try {
                Mail::to($user->email)->send(new OrderCreatedMail($order));
            } catch (\Exception $e) {
                return response()->json(['error' => 'Error al enviar el correo'], 401);
            } finally {
                return response()->json($order, 201);
            }
        } else {
            return response()->json(['error' => 'No tiene permisos'], 401);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {
        if (Gate::allows('isAdmin') || Gate::allows('isUsers', $order)) {
            try {
                $order_dates = Order::find($order->id);

                $order_with_products_and_amount = DB::select(
                    "SELECT products.*, order_product.amount FROM orders
            INNER JOIN order_product ON order_product.order_id = orders.id
            INNER JOIN products ON products.id = order_product.product_id
            WHERE orders.id = ?",
                    [$order_dates->id]
                );
                return response()->json(['order_details' => $order_dates, 'order_with_products_and_amount' => $order_with_products_and_amount], '200');
            } catch (\Exception $e) {
                return response()->json(['error' => 'Error al obtener el pedido'], 401);
            }
        } else {
            return response()->json(['error' => 'No tiene permisos'], 401);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(OrderUpdateRequest $request, Order $order)
    {
        if (Gate::denies('isAdmin')) {
            try {
                $order->direction = $request->get('direction');
                $order->post_code = $request->get('post_code');
                $order->city = $request->get('city');
                $order->state = $request->get('state');
                $order->country = $request->get('country');
                $order->save();
                $user = $order->user()->first();
            } catch (\Exception $e) {
                return response()->json(['error' => 'Error al actualizar el pedido'], 401);
            }
            try {
                Mail::to($user->email)->send(new OrderChangedMail($order));
            } catch (\Exception $e) {
                return response()->json(['error' => 'Error al enviar el correo'], 401);
            } finally {
                return response()->json($order, 200);
            }
        } else {
            return response()->json(['error' => 'No tiene permisos'], 401);
        }
    }

    public function orderStatus(OrderStatusRequest  $request, Order $order)
    {
        if (Gate::allows('isAdmin')) {
            try {
                $user = $order->user()->first();
                $order->status = $request->get('status');
                $order->save();
            } catch (\Exception $e) {
                return response()->json(['error' => 'Error al actualizar el pedido'], 401);
            }

            try {
                if ($request->get('status') === "ended") {
                    Mail::to($user->email)->send(new ValorationMail($order, Auth::user()));
                }
            } catch (\Exception $e) {
                return response()->json(['error' => 'Error al enviar el correo'], 401);
            } finally {
                return response()->json($order, 200);
            }
        } else {
            return response()->json(['error' => 'No tiene permisos'], 401);
        }
    }

    public function orderValoration(OrderValorationRequest $request, Order $order)
    {
        if (Gate::denies('isAdmin')) {
            try {
                if ($order->status != 'ended') {
                    $order->valoration = $request->get('valoration');
                    $order->save();
                    return response()->json($order, 201);
                } else {
                    return response()->json(['error' => 'No puede valorar un pedido que no esta finalizado'], 401);
                }
            } catch (\Exception $e) {
                return response()->json(['error' => 'Error al actualizar el pedido'], 401);
            }
        } else {
            return response()->json(['error' => 'No tiene permisos'], 401);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        if (Gate::allows('isAdmin') || Gate::allows('isUsers', $order)) {
            try {
                $user = $order->user()->first();
                $order_with_products_and_amount = DB::select(
                    "SELECT products.*, order_product.amount FROM orders
                INNER JOIN order_product ON order_product.order_id = orders.id
                INNER JOIN products ON products.id = order_product.product_id
                WHERE orders.id = ?",
                    [$order->id]
                );

                foreach ($order_with_products_and_amount as $product) {
                    $product_to_update = Product::find($product->id);
                    $product_to_update->amount = $product_to_update->amount + $product->amount;
                    $product_to_update->save();
                }
                DB::delete("DELETE FROM order_product WHERE order_id = ?", [$order->id]);
                $order->delete();
            } catch (\Exception $e) {
                return response()->json(['error' => 'Error al eliminar el pedido'], 401);
            }
            try{
                Mail::to($user->email)->send(new OrderDeletedMail($order));
            } catch (\Exception $e) {
                return response()->json(['error' => 'Error al enviar el correo'], 401);
            } finally {
                return response()->json(['message' => 'Pedido eliminado'], 200);
            }
        } else {
            return response()->json(['error' => 'No tiene permisos'], 401);
        }
    }

    public function getOrdersOfUser(OrdersUserRequest $request)
    {
        try{
            $order = DB::select("SELECT * FROM orders WHERE user_id = ?", [$request->get('user_id')]);
            return response()->json($order, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener los pedidos del usuario'], 401);
        }
    }
}
