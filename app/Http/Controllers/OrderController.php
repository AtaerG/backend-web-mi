<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Gate;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
                array_push($user_orders,$order);
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
    public function store(Request $request)
    {
        $order  = new Order();
        $order->total_price = $request->get('total_price');
        $order->state = $request->get('state');
        $order->user()->associate($request->get('user_id'));
        $order->paid = $request->get('paid');
        $order->save();
        return response()->json($order , 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {
        if (Gate::denies('show', $order)) {
            abort(403);
        }
        return response()->json($order, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Order $order)
    {
        if (Gate::denies('update', $order)) {
            abort(403);
        }
        $order->total_price = $request->get('total_price');
        $order->state = $request->get('state');
        $order->paid = $request->get('paid');
        $order->save();
        return response()->json($order, 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        if (Gate::denies('destroy', $order)) {
            abort(403);
        }
        $order->delete();
        return response()->json(null, 204);
    }
}
