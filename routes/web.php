<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/payment/success', function () {
    return 'Payment successful. Your request has been received!';
})->name('client.problem.success');

Route::get('/payment/cancel', function () {
    return 'Payment was canceled.';
})->name('client.problem.cancel');
