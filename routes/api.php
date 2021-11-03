<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\RegisterController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// remove sanctum routes and add pasport routes
// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::post('register', [RegisterController::class, 'register']);
Route::post('login', [RegisterController::class, 'login']);
     
Route::middleware('auth:api')->group( function () {
    // Route::resource('products', ProductController::class);
    // Properties  Routes
    Route::group(['middleware' => ['role:auditor']], function () {
        Route::resource('sections', ProductController::class);
        // Route::prefix('/sections')->group(function () {
        //     Route::resource('/', ProductController::class);
        // });
    });
    
});