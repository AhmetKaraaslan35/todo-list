<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TodoListController;
use App\Http\Controllers\TodoElementController;
use App\Http\Middleware\JwtMiddleware;

Route::middleware([JwtMiddleware::class])->group(function () {

    Route::controller(TodoListController::class)->group(function () {
        Route::get('todo/list', 'index');
        Route::post('todo/list/new', 'store');
        Route::get('todo/list/{id}', 'show');
        Route::put('todo/list/{id}', 'update');
        Route::delete('todo/list/{id}', 'destroy');
    }); 


    Route::controller(TodoElementController::class)->group(function () {
        Route::get('todo/element', 'index');
        Route::post('todo/element/new', 'store');
        Route::get('todo/element/{id}', 'show');
        Route::put('todo/element/{id}', 'update');
        Route::delete('todo/element/{id}', 'destroy');
    }); 
});
