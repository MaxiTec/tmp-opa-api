<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\SectionController;
use App\Http\Controllers\API\AreaController;
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
Route::post('login', [RegisterController::class, 'login'])->name('login');
Route::apiResource('sections', SectionController::class);
Route::post('sections/areas/{id}', [SectionController::class, 'assignAreas'])->name('assign');

    Route::prefix('/areas')->group(function () {
        Route::get('/', [AreaController::class,'index'])->name('areas.index');
        Route::get('/{id}', [AreaController::class,'show'])->name('areas.show');
        Route::put('/{id}', [AreaController::class,'update'])->name('areas.update');
    });
Route::middleware('auth:api')->group( function () {
    // Route::resource('products', ProductController::class);
    // Section  Routes
    // Route::apiResource('sections', SectionController::class);

    Route::group(['middleware' => ['role:auditor']], function () {
        Route::get('test',function () {
            return 'FUNCIONA TEIENES LOS PERMISOS';
        });
        // Route::resource('sections', ProductController::class);
        // Route::prefix('/sections')->group(function () {
        //     Route::resource('/', ProductController::class);
        // });
    });
});