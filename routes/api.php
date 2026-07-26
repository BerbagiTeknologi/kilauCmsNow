<?php

use App\Http\Controllers\AdminPage\Api\IklanDonasiAPIController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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


/* Data Route Iklan Donasi */
Route::get('iklandonasi', [IklanDonasiAPIController::class, 'index']);
Route::get('iklandonasi/{id}', [IklanDonasiAPIController::class, 'show']);
Route::post('iklandonasi-create', [IklanDonasiAPIController::class, 'create']);
Route::post('iklandonasi-edit/{id}', [IklanDonasiAPIController::class, 'edit']);
Route::delete('iklandonasi-delete/{id}', [IklanDonasiAPIController::class, 'delete']);