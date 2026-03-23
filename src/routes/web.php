<?php

use App\Http\Controllers\ItemController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\PurchaseAddressController;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/


Route::get('/', [ItemController::class, 'index'])->name('index');
Route::get('/items', [ItemController::class, 'index'])->name('items.index');
Route::get('/item/{item}', [ItemController::class, 'show'])->name('items.show');


Route::get('/purchase/success', [PurchaseController::class, 'success'])->name('purchase.success');
Route::get('/purchase/cancel', [PurchaseController::class, 'cancel'])->name('purchase.cancel');


Route::middleware(['auth'])->group(function () {


    Route::get('/mypage', [ProfileController::class, 'show'])->name('mypage');
    Route::get('/mypage/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/mypage/profile', [ProfileController::class, 'store'])->name('profile.store');
    Route::put('/mypage/profile', [ProfileController::class, 'update'])->name('profile.update');

    
    Route::post('/favorite/{item}', [FavoriteController::class, 'toggle'])->name('favorite.toggle');

    
    Route::post('/item/{item}/comment', [CommentController::class, 'store'])->name('comment.store');


    Route::get('/purchase/{item}', [PurchaseController::class, 'show'])->name('purchase.show');
    Route::post('/purchase/{item}', [PurchaseController::class, 'store'])->name('purchase.store');
    Route::post('/purchase/{item}/pay', [PurchaseController::class, 'pay'])->name('purchase.pay');

    
    Route::get('/purchase/address/{item}', [PurchaseAddressController::class, 'edit'])->name('purchase.address.edit');
    Route::post('/purchase/address/{item}', [PurchaseAddressController::class, 'update'])->name('purchase.address.update');

    
    Route::get('/sell', [ItemController::class, 'create'])->name('items.create');
    Route::post('/sell', [ItemController::class, 'store'])->name('items.store');


});
