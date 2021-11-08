<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\SectionController;
use App\Http\Controllers\API\AreaController;
use App\Http\Controllers\API\PropertyController;
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
/*:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
::::::::::::::::::::::::::::::::: SECTION ROUTES:::::::::::::::::::::::::::
:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::*/

Route::apiResource('sections', SectionController::class);
Route::post('sections/areas/{id}', [SectionController::class, 'assignAreas'])->name('assign');


/*:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
::::::::::::::::::::::::::::::::: AREAS ROUTES:::::::::::::::::::::::::::::
:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::*/


Route::prefix('/areas')->group(function () {
    Route::get('/', [AreaController::class,'index'])->name('areas.index');
    Route::get('/{id}', [AreaController::class,'show'])->name('areas.show');
    Route::put('/{id}', [AreaController::class,'update'])->name('areas.update');
    Route::delete('/{id}', [AreaController::class,'destroy'])->name('areas.delete');
    Route::get('/{id}/status', [AreaController::class,'toggleStatus'])->name('areas.status');
    Route::get('/{id}/active', [AreaController::class,'toggleActive'])->name('areas.active');
    Route::post('/{id}/assign', [AreaController::class,'assignCriteria'])->name('areas.assign');
});

/*:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
::::::::::::::::::::::::::::::::: AREAS ROUTES:::::::::::::::::::::::::::::
:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::*/
Route::apiResource('properties', PropertyController::class);

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