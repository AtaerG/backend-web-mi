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
        try {
            $product = Product::get();
            return response()->json($product, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error a la hora de mostrar todos los productos.'], 500);
        }
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
            try {
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
            } catch (\Exception $e) {
                return response()->json(['error' => 'Error al crear el producto.'], 500);
            }
        } else {
            return response()->json(['error' => 'No tiene permisos'], 401);
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
        try{
            $product_amount = DB::select("SELECT amount FROM products WHERE id = " . $product->id);
            $cmnt_products_with_orders_and_names =
                DB::select("SELECT DISTINCT comments.*, users.name, users.surname, orders.valoration as valoration_order, orders.id as id_of_order, country FROM orders
                        INNER JOIN order_product ON orders.id = order_product.order_id
                        INNER JOIN comments ON orders.user_id = comments.user_id AND order_product.product_id = comments.product_id AND orders.id = comments.order_id
                        INNER JOIN users ON users.id = comments.user_id and orders.user_id = users.id
                        INNER JOIN products ON  products.id = order_product.product_id WHERE products.id = " . $product->id
                    . " GROUP BY comments.id, users.name, users.surname, orders.valoration, orders.id, country");
            return response()->json(['product' => $product, 'comments' => $cmnt_products_with_orders_and_names, 'amount' => $product_amount], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al mostrar el producto.'], 500);
        }
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
        try{
            $product->name = $request->get('name');
            $product->price = $request->get('price');
            $product->description = $request->get('description');
            $product->amount = $request->get('amount');
            $product->image_url = $request->get('image_url');
            $product->tag = $request->get('tag');
            $product->visible = $request->get('visible');
            $product->save();
            return response()->json($product, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al actualizar el producto.'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function deleteProduct(ProductsVisibilityRequest $request, Product $product)
    {
        try{
            $product->visible = $request->get('visible');
            $product->save();
            return response()->json($product, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al eliminar el producto.'], 500);
        }
    }
}
