<?php

use App\Http\Controllers\Api\TimbangController;
use App\Http\Controllers\AuthController;
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

route::get('/spmlist', [TimbangController::class, 'index'])->middleware('auth:sanctum');
route::get('/siapKeluar', [TimbangController::class, 'siapKeluar'])->middleware('auth:sanctum');
route::post('/loginUser', [AuthController::class, 'loginUser']);
route::post('/timbangin', [TimbangController::class, 'timbangin'])->middleware('auth:sanctum');
route::post('/timbangout', [TimbangController::class, 'timbangout'])->middleware('auth:sanctum');
    