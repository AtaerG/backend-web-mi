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
            //pass array of products to array
            foreach (json_decode(json_decode($request->get('products'))) as $product) {
                DB::table('order_product')->insert(
                    ['product_id' => $product->product->id, 'amount' => $product->amount, 'order_id' => $order->id]
                );
            }
            $user = $order->user()->first();
            //Mail::to($user->email)->send(new OrderCreatedMail($order));
            return response()->json($order, 201);
        } else {
            return response()->json(['error' => 'No tiene permisos para hacer esta accion'], 401);
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
            $order_dates = Order::find($order->id);

        $order_with_products_and_amount = DB::select(
            "SELECT products.*, order_product.amount FROM orders
            INNER JOIN order_product ON order_product.order_id = orders.id
            INNER JOIN products ON products.id = order_product.product_id
            WHERE orders.id = ?",
            [$order_dates->id]);
        return response()->json(['order_details'=> $order_dates, 'order_with_products_and_amount'=> $order_with_products_and_amount], '200');
        } else {
            return response()->json(['error' => 'No tiene permisos para hacer esta accion'], 401);
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
            $order->direction = $request->get('direction');
            $order->post_code = $request->get('post_code');
            $order->city = $request->get('city');
            $order->state = $request->get('state');
            $order->country = $request->get('country');
            $order->save();
            $user = $order->user()->first();
            Mail::to($user->email)->send(new OrderChangedMail($order));
            return response()->json($order, 201);
        } else {
            return response()->json(['error' => 'No tiene permisos para hacer esta accion'], 401);
        }
    }

    public function orderStatus(OrderStatusRequest  $request, Order $order)
    {
        if (Gate::allows('isAdmin')) {
        $user = $order->user()->first();
        $order->status = $request->get('status');
        if ($request->get('status') === "ended") {
            Mail::to($user->email)->send(new ValorationMail($order, Auth::user()));
        }
        $order->save();
        return response()->json($order, 201);
        } else {
            return response()->json(['error' => 'No tiene permisos para hacer esta accion'], 401);
        }
    }

    public function orderValoration(OrderValorationRequest $request, Order $order)
    {
        if (Gate::denies('isAdmin')) {
            if ($order->status != 'ended') {
                $order->valoration = $request->get('valoration');
                $order->save();
                return response()->json($order, 201);
            } else {
                return response()->json(['error' => 'No puede valorar un pedido que no ha finalizado'], 401);
            }
        } else {
            return response()->json(['error' => 'No tiene permisos para hacer esta accion'], 401);
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

            $user = $order->user()->first();
            $order_with_products_and_amount = DB::select(
                "SELECT products.*, order_product.amount FROM orders
                INNER JOIN order_product ON order_product.order_id = orders.id
                INNER JOIN products ON products.id = order_product.product_id
                WHERE orders.id = ?",
            [$order->id]);

            foreach ($order_with_products_and_amount as $product) {
                $product_to_update = Product::find($product->id);
                $product_to_update->amount = $product_to_update->amount + $product->amount;
                $product_to_update->save();
            }
            DB::delete("DELETE FROM order_product WHERE order_id = ?", [$order->id]);
            Mail::to($user->email)->send(new OrderDeletedMail($order));
            $order->delete();
            return response()->json(null, 204);
        } else {
            return response()->json(['error' => 'No tiene permisos para hacer esta accion'], 401);
        }
    }

    public function getOrdersOfUser(OrdersUserRequest $request)
    {
        $order = DB::select("SELECT * FROM orders WHERE user_id = ?", [$request->get('user_id')]);
        return response()->json($order, 200);
    }
}
