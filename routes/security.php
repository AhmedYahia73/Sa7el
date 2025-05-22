<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route; 

Route::middleware(['auth:sanctum', 'IsSecurity'])->group(function(){

});