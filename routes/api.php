<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Salary Master API Routes
Route::middleware('auth:sanctum')->prefix('salary')->group(function () {
    // Salary Setup APIs
    Route::get('/setup', [App\Http\Controllers\Api\SalaryMasterApiController::class, 'salarySetupIndex']);
    Route::post('/setup', [App\Http\Controllers\Api\SalaryMasterApiController::class, 'salarySetupStore']);
    Route::get('/setup/{id}', [App\Http\Controllers\Api\SalaryMasterApiController::class, 'salarySetupShow']);
    Route::put('/setup/{id}', [App\Http\Controllers\Api\SalaryMasterApiController::class, 'salarySetupUpdate']);
    
    // Salary Advance APIs
    Route::get('/advance', [App\Http\Controllers\Api\SalaryMasterApiController::class, 'salaryAdvanceIndex']);
    Route::post('/advance', [App\Http\Controllers\Api\SalaryMasterApiController::class, 'salaryAdvanceStore']);
    Route::get('/advance/{id}', [App\Http\Controllers\Api\SalaryMasterApiController::class, 'salaryAdvanceShow']);
    Route::put('/advance/{id}', [App\Http\Controllers\Api\SalaryMasterApiController::class, 'salaryAdvanceUpdate']);
    
    // Salary Processing APIs
    Route::get('/processing', [App\Http\Controllers\Api\SalaryMasterApiController::class, 'salaryProcessingIndex']);
    Route::post('/processing', [App\Http\Controllers\Api\SalaryMasterApiController::class, 'salaryProcessingStore']);
    Route::get('/processing/{id}', [App\Http\Controllers\Api\SalaryMasterApiController::class, 'salaryProcessingShow']);
    Route::put('/processing/{id}', [App\Http\Controllers\Api\SalaryMasterApiController::class, 'salaryProcessingUpdate']);
    Route::post('/processing/{id}/mark-paid', [App\Http\Controllers\Api\SalaryMasterApiController::class, 'markPaid']);
});
