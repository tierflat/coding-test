<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ObjectController;
use App\Http\Middleware\EnsureTokenIsValid;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/object', [ObjectController::class, 'store']);
Route::get('/object/get_all_records', [ObjectController::class, 'get_all_records'])->middleware(EnsureTokenIsValid::class);
Route::get('/object/{key}', [ObjectController::class, 'show']);

Route::any('{any}', function(){
    return response()->json([
        'status'    => false,
        'message'   => 'Page Not Found.',
    ], 404);
})->where('any', '.*');
                                                                                                                                    