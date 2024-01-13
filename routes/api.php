<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\parkingController;

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

Route::middleware('auth:sanctum')->get(
    '/user', function (Request $request) {
        return $request->user();
    }
);

Route::controller(parkingController::class)->group(
    function () {
        Route::get('/pricing', 'price');
        Route::post('/parking', 'book');
        Route::delete('/parking', 'cancel');
        Route::put('/parking', 'edit');
    }
);
