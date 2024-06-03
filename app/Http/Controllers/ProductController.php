<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class ProductController extends Controller
{
    public function __construct()
    {
    }
    public function getallproduct()
    {
        try {
            $products = Product::get();

            if ($products->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'No products found',
                ], 404);
            }

            return response()->json([
                'status' => true,
                'data' => $products,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => "Internal Server Error",
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getsingleproduct(string $id)
    {
        try {
            $product = Product::find($id);

            if (!$product) {
                return response()->json([
                    'status' => false,
                    'message' => 'Product not found',
                ], 404);
            }

            return response()->json([
                'status' => true,
                'data' => $product,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => "Internal Server Error",
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getproductbyBrand(string $id)
    {
    }
    public function getproductbyCategory(string $id)
    {
    }
    public function addproduct(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'img' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $validator->errors()->first(),
                    'errors' => $validator->errors()
                ], 422);
            }

            $productData = array_merge($validator->validated(), [
                'rating' => [
                    '5star' => 0,
                    '4star' => 0,
                    '3star' => 0,
                    '2star' => 0,
                    '1star' => 0,
                    'color' => 'red'
                ],
            ]);

            Product::create($productData);
            return response()->json([
                'status' => true,
                'message' => 'Product added successfully',
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => "Internal Server Error",
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function updateproduct(Request $request)
    {
        
    }
    public function deleteproduct(string $id)
    {
    }
}
