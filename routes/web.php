<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::get('/pos', function () {
    return view('pos');
})->name('pos');


Route::get('/products', function () {
    return view('products');
})->name('products');

Route::get('/orders', function () {
    return view('orders');
})->name('orders');