<?php

namespace App\Http\Controllers;

use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProductsController extends Controller
{
    public function addProduct(Request $request)
    {
        $validator = Validator($request->all(), [
            'name' => 'required|string|max:100',
            'price' => 'required|numeric',
            'description' => 'required|string|max:600',
            'image_url' => 'required|string|max:255',
            'stock' => 'required|numeric',
            'is_active' => 'required|boolean',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
        $product = Products::create([
            'name' => $request->get('name'),
            'price' => $request->get('price'),
            'description' => $request->get('description'),
            'image_url' => $request->get('image_url'),
            'stock' => $request->get('stock'),
            'is_active' => $request->get('is_active'),

        ]);
        return response()->json([
            'message' => 'Product added successfully',
            'product' => $product,
        ], 201);
    }

    ## Get all products
    public function getProducts(Request $request)
    {
        $query = Products::query()
            ->leftJoin('vauling_product', 'products.id', '=', 'vauling_product.product_id')
            ->select(
                'products.*', // Selecciona todos los campos de products
                DB::raw('COALESCE(AVG(vauling_product.quantity), 0) as average_rating') // Calcula el promedio de quantity
            )
            ->groupBy('products.id'); // Agrupa por producto para que el AVG funcione correctamente

        // Aplicar filtros de búsqueda
        if ($request->has('name')) {
            $query->where('products.name', 'like', '%' . $request->name . '%');
        }
        if ($request->has('description')) {
            $query->where('products.description', 'like', '%' . $request->description . '%');
        }
        if ($request->has('min_price')) {
            $query->where('products.price', '>=', $request->min_price);
        }
        if ($request->has('max_price')) {
            $query->where('products.price', '<=', $request->max_price);
        }
        if ($request->has('min_stock')) {
            $query->where('products.stock', '>=', $request->min_stock);
        }
        if ($request->has('max_stock')) {
            $query->where('products.stock', '<=', $request->max_stock);
        }
        if ($request->has('is_active')) {
            $statuses = explode(',', $request->is_active);
            $query->whereIn('products.is_active', $statuses);
        }
        if ($request->has('sort_by')) {
            $sortField = $request->sort_by;
            $sortDirection = $request->has('sort_order') && strtolower($request->sort_order) === 'desc' ? 'desc' : 'asc';

            if (in_array($sortField, ['name', 'price', 'stock', 'is_active', 'average_rating'])) {
                $query->orderBy($sortField, $sortDirection);
            }
        }
        if ($request->has('start_date')) {
            $query->whereDate('products.created_at', '>=', $request->start_date);
        }

        $products = $query->get();

        return response()->json($products, 200);
    }

    ## Get products by ID
    public function getProductById($id)
    {
        $product = Products::leftJoin('vauling_product', 'products.id', '=', 'vauling_product.product_id')
            ->select(
                'products.*',
                DB::raw('COALESCE(AVG(vauling_product.quantity), 0) as average_rating')
            )
            ->where('products.id', $id)
            ->groupBy('products.id')
            ->first();

        if (!$product) {
            return response()->json(['message' => 'Product not Found'], 404);
        }

        return response()->json($product, 200);
    }


    ## Update Product by ID
    public function updateProductById(Request $request, $id)
    {
        $products = Products::find($id);
        if (!$products) {
            return response()->json(['message' => 'Product not Found'], 404);
        }
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:100',
            'price' => 'sometimes|numeric',
            'description' => 'sometimes|string|max:600',
            'image_url' => 'sometimes|string|max:255',
            'stock' => 'sometimes|numeric|min:0',
            'is_active' => 'sometimes|boolean',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
        if ($request->has('name')) {
            $products->name = $request->name;
        }
        if ($request->has('price')) {
            $products->price = $request->price;
        }
        if ($request->has('description')) {
            $products->description = $request->description;
        }
        if ($request->has('image_url')) {
            $products->image_url = $request->image_url;
        }
        if ($request->has('stock')) {
            $products->stock = $request->stock;
        }
        if ($request->has('is_active')) {
            $products->is_active = $request->is_active;
        }
        $products->update();

        return response()->json([
            'message' => 'Product updated successfully',
            'product' => $products,
        ], 200);
    }

    ## Delete function

    public function deleteProductById($id)
    {
        $products = Products::find($id);
        if (!$products) {
            return response()->json(['message' => 'Product not Found'], 404);
        }
        $products->delete();
        return response()->json([
            'message' => 'Product deleted successfully',
        ], 200);
    }
}
