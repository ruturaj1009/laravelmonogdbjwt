<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Review;
use App\Models\User;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReviewController extends Controller
{
    public function __construct()
    {
    }

    public function addReview($pid, Request $request)
    {
        try {
            $product = Product::find($pid);
            if (!$product) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid product ID'
                ], 404);
            }

            $user = Auth::user();
            $validator = Validator::make($request->all(), [
                'review' => 'required',
                'star' => 'required|integer|min:1|max:5',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $validator->errors()->first(),
                    'errors' => $validator->errors()
                ], 422);
            }

            $review = Review::find($user->id);
            if ($review) {
                return response()->json([
                    'status' => false,
                    'message' => 'Already Reviewed',
                ], 404);
            }
            $reviewData = array_merge($validator->validated(), [
                'uid' => $user->id,
                'pid' => $pid,
            ]);

            Review::create($reviewData);

            $product = Product::find($pid);
            if ($product) {
                $ratingKey = $request->star . 'star';
                $rating = $product->rating;
                $rating[$ratingKey] = $rating[$ratingKey] + 1;
                $product->rating = $rating;
                $product->save();
            }
            return response()->json([
                'status' => true,
                'message' => 'Review sent for verification',
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => "Internal Server Error",
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
