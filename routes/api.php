<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\LoginController;

Route::controller(LoginController::class)
->group(function() {
    Route::get('logout', 'logout')->middleware(['auth:sanctum']);
    Route::post('delete_account', 'delete_account')->middleware(['auth:sanctum']);
    Route::post('security/login', 'security_login');
    Route::post('admin/login', 'admin_login');
    Route::post('village/login', 'village_login');
    Route::post('user/login', 'user_login');
    Route::post('user/sign_up', 'sign_up');
    Route::get('user/sign_up_list', 'sign_up_list');
});
// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
