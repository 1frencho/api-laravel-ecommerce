<?php

namespace App\Http\Controllers;

use App\Models\Products;
use App\Models\VaulingProduct;
use Illuminate\Http\Request;
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
        $valuingProduct = VaulingProduct::all();
        return response()->json($valuingProduct, 200);
    }
}
