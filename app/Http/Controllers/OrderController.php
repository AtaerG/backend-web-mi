<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use App\Mail\OrderCreatedMail;
use App\Mail\ValorationMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

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
        $order->status = $request->get('status');
        $order->direction = $request->get('direction');
        $order->post_code = $request->get('post_code');
        $order->city = $request->get('city');
        $order->state = $request->get('state');
        $order->country = $request->get('country');
        $order->user()->associate(Auth::user()->id);
        $order->save();
        $array_prod_ids = explode(',',$request->get('products'));
        $products = Product::find($array_prod_ids);
        $order->products()->attach($products);
        Mail::to("ataerg.web-designer@outlook.com")->send(new OrderCreatedMail($order));
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
        $order_with_products = Order::where('id', '=', $order->id)->with('products')->first();
        return response()->json($order_with_products, 200);
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
        if($request->get('state')==="ended"){
            Mail::to("ataerg.web-designer@outlook.com")->send(new ValorationMail($order, Auth::user()));
        }
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

    public function getOrdersOfUser(Request $request){
        $order = DB::select("SELECT * FROM orders WHERE user_id = ?", [$request->get('user_id')]);
        //$order_of_user_with_products = $order->where('id', '=', $order->id)->with('products')->first();
        return response()->json($order, 200);
    }
}
