<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReviewController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//No Route
Route::fallback(function () {
    return response()->json([
        'status' => false,
        'message' => "No Route found",
    ], 404); 
  });

Route::get('/v1',function (){
    return response()->json([
        'status' => true,
        'message' => 'server is listening'
    ],200);
});

//Auth Routes
Route::group(['middleware'=>'api','prefix'=>'v1/user'],function($router){
    //user routes
    Route::post('/register',[AuthController::class,'register']); //done
    Route::post('/login',[AuthController::class,'login']); //done
    Route::post('/logout',[AuthController::class,'logout']); //done
    Route::get('/profile',[AuthController::class,'profile']); //done

    //makeadmin
    Route::post('/send-otp',[AuthController::class,'send-otp']); 
    Route::post('/send-otp',[AuthController::class,'verify-otp']); 

    //admin routes
    Route::get('/getall',[AuthController::class,'allusers'])->middleware('isadmin'); //done
    Route::get('/{id}',[AuthController::class,'singleuser'])->middleware('isadmin'); //done
});

//Product Routes
Route::group(['prefix'=>'v1/product'],function($router){
    //public routes
    Route::get('/all',[ProductController::class,'getallproduct']); //done
    Route::get('/{id}',[ProductController::class,'getsingleproduct']); //done
    Route::get('/b/{id}',[ProductController::class,'getproductbyBrand']); 
    Route::get('/c/{id}',[ProductController::class,'getproductbyCategory']);

    //admin routes
    Route::post('/add',[ProductController::class,'addproduct'])->middleware('isadmin'); //done
    Route::patch('/update',[ProductController::class,'updateproduct'])->middleware('isadmin');
    Route::delete('/delete',[ProductController::class,'deleteproduct'])->middleware('isadmin');
});

//Review Routes
Route::group(['middleware'=>'api','prefix'=>'v1/review'],function($router){
    //admin routes
    Route::patch('/update',[ReviewController::class,'updatereview']);
    Route::post('/delete',[ReviewController::class,'deletereview']);
    
    //user routes
    Route::post('/add/{pid}',[ReviewController::class,'addreview']); //done

    //public routes
    Route::get('/all',[ReviewController::class,'getallreview']); 
    Route::get('/{id}',[ReviewController::class,'getsinglereview']);
});

