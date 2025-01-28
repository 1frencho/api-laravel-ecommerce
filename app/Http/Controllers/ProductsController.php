<?php

namespace App\Http\Controllers;

use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductsController extends Controller
{
    public function addProduct(Request $request){
        $validator = Validator($request-> all(),[
            'name'=> 'required|string| min:10|max:100',
            'price'=> 'required|numeric',
        ]);
        if($validator->fails()){
            return response()->json(['error' => $validator->errors()],422);
        }
        Products::create([
            'name'=>$request->get('name'),
            'price'=>$request->get('price'),

        ]);
        return response()->json(['message'=>'product added succesfully'], 201);
    }

    ## Get all products
    public function getProducts(){
        $products = Products::all();
        if($products->isEmpty()){
            return response()-> json(['message'=>'No Products Found'], 404);
        }
        return response()->json($products,200);
    }

    ## Get products by ID
    public function getProductById($id){
        $products = Products::find($id);
        if($products){
            return response()-> json(['message'=>'Product not Found'], 404);
        }
        return response()->json($products,200);
    }

    ## Update Product by ID
    public function updateProductById(Request $request, $id){
        $products = Products::find($id);
        if(!$products){
            return response()-> json(['message'=>'Product not Found'], 404);
        }
        $validator = Validator::make($request-> all(),[
            'name'=> 'sometimes|string| min:10|max:100',
            'price'=> 'sometimes|numeric',
        ]);
        if($validator->fails()){
            return response()->json(['error' => $validator->errors()],422);
        }
        if ($request->has('name')){
            $products->name = $request->name;
        }
        if ($request->has('price')){
            $products->price = $request->price;
        }
        $products->update();
        return response()-> json(['message'=>'product updated succesfully'],200);
    }

    ## Delete function

    public function deleteProductById($id){
        $products = Products::find($id);
        if(!$products){
            return response()-> json(['message'=>'Product not Found'], 404);
        }
        $products->delete();
    }

}
