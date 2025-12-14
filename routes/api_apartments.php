<?php

use App\Http\Controllers\Api\Apartments\ApartmentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('apartments')->group(function () {
    
    Route::get('/index', [ApartmentController::class, 'index'])->name('apartments.index'); 
    Route::get('/show/{id}', [ApartmentController::class, 'show'])->name('apartments.show');
    Route::get('/filter', [ApartmentController::class, 'filter'])->name('apartments.filter');
});


Route::prefix('apartments')->middleware('auth:sanctum')->group(function () {
    
    Route::post('/store', [ApartmentController::class, 'store'])->name('apartments.store');
    
    Route::put('/update/{id}', [ApartmentController::class, 'update'])->name('apartments.update');
    
    Route::delete('/delete/{id}', [ApartmentController::class, 'destroy'])->name('apartments.delete');
});