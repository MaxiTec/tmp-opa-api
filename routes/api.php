<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\SectionController;
use App\Http\Controllers\API\CriteriaController;
use App\Http\Controllers\API\AreaController;
use App\Http\Controllers\API\PropertyController;
use App\Http\Controllers\API\ProgramController;
use App\Http\Controllers\API\RoleController;
use App\Http\Controllers\API\AuditController;
use App\Http\Controllers\API\UserController;
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

// Public routes
Route::post('/login', [RegisterController::class, 'login'])->name('login');



Route::prefix('/programs')->group(function () {
    Route::get('/', [ProgramController::class,'index'])->name('programs.index');
});
/*:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
::::::::::::::::::::::::::::::::: AREAS ROUTES:::::::::::::::::::::::::::::
:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::*/
// Route::apiResource('properties', PropertyController::class);



Route::middleware('auth:api')->group( function () {

    Route::post('register', [RegisterController::class, 'register']);
    Route::post('logout', [RegisterController::class, 'logout'])->name('logout');

    Route::prefix('/audits')->group(function () {
        Route::get('/', [AuditController::class,'index'])->name('audit.index');
        Route::get('/{id}', [AuditController::class,'show'])->name('audit.show');
        Route::post('/{id}', [AuditController::class,'store'])->name('audit.create');
        Route::post('/{id}/check', [AuditController::class,'checkAudit'])->name('audit.check');
    });

    // Properties Routes
    Route::prefix('/properties')->group(function () {
        Route::get('/', [PropertyController::class,'index'])->name('properties.index');
        Route::post('/', [PropertyController::class,'store'])->name('properties.create');
        Route::get('/{id}', [PropertyController::class,'show'])->name('properties.show');
        // This route would be change if we store images
        Route::put('/{id}', [PropertyController::class,'update'])->name('properties.update');
        Route::delete('/{id}', [PropertyController::class,'destroy'])->name('properties.delete');
        Route::get('/{id}/active', [PropertyController::class,'toggleActive'])->name('properties.active');
        Route::get('/{id}/catalog', [PropertyController::class,'showCriteria'])->name('properties.catalog');
        Route::post('/{id}/assign', [PropertyController::class,'AssignToProperties'])->name('properties.assign');
        Route::post('/{id}/show', [PropertyController::class,'ProgramByHotel'])->name('properties.showByHotel');
        Route::post('/{id}/duplicate', [PropertyController::class,'duplicate'])->name('properties.duplicate');
    });

    // Route::group(['middleware' => ['role:administrator']], function () {
    //     Route::apiResource('roles', RoleController::class);
    // });
    Route::group(['middleware' => ['role:administrator']], function () {
        Route::get('/user/{user}/active', [UserController::class,'disable'])->name('user.disable');
        Route::apiResource('user', UserController::class);
    });
    
    Route::apiResource('roles', RoleController::class);

    /*:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
    ::::::::::::::::::::::::::::::::: SECTION ROUTES:::::::::::::::::::::::::::
    :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::*/
    Route::get('sections-formatted', [SectionController::class,'getAllFormattedResources']);
    Route::apiResource('sections', SectionController::class);
    Route::post('sections/{id}/area', [SectionController::class, 'assignAreas'])->name('sections.assign');
    Route::get('sections/{id}/active', [SectionController::class,'toggleActive'])->name('sections.active');

    Route::apiResource('criteria', CriteriaController::class);
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
       Route::post('/{id}/add-question', [AreaController::class,'addQuestion'])->name('sections.addQuestion');
       Route::post('/{id}/remove-question', [AreaController::class,'removeQuestion'])->name('sections.removeQuestion');
       Route::post('/{id}/update-question', [CriteriaController::class,'update'])->name('sections.updateQuestion');

    });
});