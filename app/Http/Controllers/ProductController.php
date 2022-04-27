<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Requests\ProductRequest;
use App\Http\Requests\ProductsVisibilityRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $product = Product::get();
        return response()->json($product, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductRequest $request)
    {
        if (Gate::allows('isAdmin')) {
            $product = new Product();
            $product->name = $request->get('name');
            $product->price = $request->get('price');
            $product->description = $request->get('description');
            $product->amount = $request->get('amount');
            $product->image_url = $request->get('image_url');
            $product->price_descount = $request->get('price_descount');
            $product->tag = $request->get('tag');
            $product->visible = $request->get('visible');
            $product->save();
            return response()->json($product, 201);
        } else {
            return response()->json(['error' => 'No tiene permisos para hacer esta accion'], 401);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        $product_amount = DB::select("SELECT amount FROM products WHERE id = ".$product->id);
        $comments_with_names = DB::select("SELECT comments.*, users.name, users.surname FROM users INNER JOIN comments ON users.id = comments.user_id");
        return response()->json(['product' => $product, 'comments' => $comments_with_names, 'amount'=> $product_amount], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(ProductRequest $request, Product $product)
    {
            $product->name = $request->get('name');
            $product->price = $request->get('price');
            $product->description = $request->get('description');
            $product->amount = $request->get('amount');
            $product->image_url = $request->get('image_url');
            $product->tag = $request->get('tag');
            $product->visible = $request->get('visible');
            $product->save();
            return response()->json($product, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function deleteProduct(ProductsVisibilityRequest $request, Product $product)
    {
        $product->visible = $request->get('visible');
        $product->save();
        return response()->json($product, 200);
    }
}
