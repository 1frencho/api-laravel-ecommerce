<?php

namespace App\Http\Controllers;

use App\Models\Products;
use App\Models\VaulingProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class VaulingProductController extends Controller
{
    //metodo para dar valoracion a un producto
    public function rateProduct(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // 'user_id' => 'required|integer|exists:users,id',
            'product_id' => 'required|integer|exists:products,id',
            'quantity' => 'required|integer|between:1,10',
            'comment' => 'string|nullable'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Current id of authenticated user. If this is sent by request, it allows to rate a product of another user...
        $user_id = $request->user()->id;


        $vaulingProduct = new VaulingProduct();
        // $vaulingProduct->user_id = $request->user_id;
        $vaulingProduct->user_id = $user_id;
        $vaulingProduct->product_id = $request->product_id;
        $vaulingProduct->quantity = $request->quantity;
        $vaulingProduct->comment = $request->comment;
        $vaulingProduct->save();

        return response()->json(['message' => 'Valoracion ingresada correctamente', 'user' => $user_id], 201);
    }

    //metodo para calcular el promedio de valoracion de un producto
    public function getAverageRating($id)
    {
        $validator = Validator::make(['product_id' => $id], [
            'product_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $vaulingProduct = VaulingProduct::where('product_id', $id)->get();
        $total = 0;
        $count = 0;
        foreach ($vaulingProduct as $vauling) {
            $total += $vauling->quantity;
            $count++;
        }

        if ($count == 0) {
            return response()->json(['message' => 'No hay valoraciones para este producto'], 200);
        }

        $average = $total / $count;
        return response()->json(['averageRaiting' => $average], 200);
    }

    //metodo para obtener el producto con mayor valoracion
    public function getBestProduct()
    {
        $vaulingProduct = VaulingProduct::all();
        $bestProduct = null;
        $bestRating = 0;
        foreach ($vaulingProduct as $vauling) {
            if ($vauling->quantity > $bestRating) {
                $bestRating = $vauling->quantity;
                $bestProduct = $vauling->product_id;
            }
        }

        //traer datos del mejor producto
        $bestProduct = Products::find($bestProduct);

        return response()->json(['bestProduct' => $bestProduct], 200);
    }

    public function getPublicProductRatings()
    {
        $valuingProduct = DB::table('vauling_product')
            ->join('products', 'vauling_product.product_id', '=', 'products.id')
            ->join('users', 'vauling_product.user_id', '=', 'users.id')
            ->select(
                'vauling_product.quantity',
                'vauling_product.product_id',
                'users.name as user_name',
                'vauling_product.comment',
                'vauling_product.id',
                'products.name as product_name', // Nombre del producto
                'products.image_url' // Imagen del producto
            )
            ->where('vauling_product.quantity', '>', 0)
            ->orderBy('vauling_product.quantity', 'desc')
            ->get();

        return response()->json($valuingProduct, 200);
    }

    public function getProductRatingsById($product_id)
    {
        $reviews = DB::table('vauling_product')
            ->join('products', 'vauling_product.product_id', '=', 'products.id')
            ->join('users', 'vauling_product.user_id', '=', 'users.id')
            ->select(
                'vauling_product.id',
                'vauling_product.quantity',
                'vauling_product.comment',
                'users.name as user_name',
                'products.name as product_name',
                'products.id as product_id',
                'products.image_url',
                'vauling_product.created_at',
            )
            ->where('vauling_product.product_id', $product_id)
            ->where('vauling_product.quantity', '>', 0) // Solo reviews con rating válido
            ->orderBy('vauling_product.created_at', 'desc') // Ordenar por fecha de creación
            ->get();

        return response()->json($reviews, 200);
    }
}
