<?php

namespace App\Http\Controllers;

use App\Models\VaulingProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VaulingProductController extends Controller
{
    //metodo para dar valoracion a un producto
    public function rateProduct(Request $request) {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'product_id' => 'required|integer',
            'quantity' => 'required|integer|between:1,10',
            'comment' => 'string|nullable'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $vaulingProduct = new VaulingProduct();
        $vaulingProduct->user_id = $request->user_id;
        $vaulingProduct->product_id = $request->product_id;
        $vaulingProduct->quantity = $request->quantity;
        $vaulingProduct->comment = $request->comment;
        $vaulingProduct->save();

        return response()->json(['message' => 'Valoracion ingresada correctamente'], 201);
    }
}
