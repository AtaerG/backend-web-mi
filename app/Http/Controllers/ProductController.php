<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Requests\ProductRequest;
use App\Models\Comment;
use Illuminate\Support\Facades\DB;

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
        $product = new Product();
        $product->name = $request->get('name');
        $product->price = $request->get('price');
        $product->description = $request->get('description');
        $product->amount = $request->get('amount');
        $product->image_url = $request->get('image_url');
        $product->price_descount = $request->get('price_descount');
        $product->tag = $request->get('tag');
        $product->save();
        return response()->json($product, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        //$comment = Comment::get();
        //$comments_of_products = $product->comments;
        //$comments_of_product = DB::select("SELECT user_id from comments where product_id = :id", ['id'=> $product->id]);
        //$user_name = DB::select("SELECT name, surname FROM users WHERE id = :id", ['id' => $comments_of_products->product->user_id]);
        $comments_with_names = DB::select("SELECT comments.*, users.name, users.surname FROM users INNER JOIN comments ON users.id = comments.user_id");
        //$user_name = DB::table('users')->where('id', $comments_of_product->user_id)->value('name');
        //$comments_of_products = $product->comments;
        return response()->json(['product'=>$product, 'comments'=>$comments_with_names], 200);
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
        $product->save();
        return response()->json($product,200);
    }

    public function addAmount(Product $product)
    {
        if($product->amount > 0){
            $product->amount =  $product->amount+1;
            $product->save();
            //return response()->json($product,200);
        } else {
            //return response()->json(['error'=> 'Ya no hay mas producto!'],200);
        }

    }
    /*
    public function reduceAmount(Request $request)
    {
        if($product->amount > 0){
            $product->amount =  $product->amount-1;
            $product->save();
            //return response()->json($product,200);
        } else {
            //return response()->json(['error'=> 'Ya no hay mas producto!'],200);
        }
    }
    */
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        $product->delete();
        return response()->json(null, 204);
    }
}
