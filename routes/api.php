<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// Route::post('/object', [KeyValueController::class, 'store']);
// Route::get('/object/get_all_records', [KeyValueController::class, 'index']);
// Route::get('/object/{key}', [KeyValueController::class, 'show']);

// Route::any('{any}', function(){
//     return response()->json([
//         'status'    => false,
//         'message'   => 'Page Not Found.',
//     ], 404);
// })->where('any', '.*');
