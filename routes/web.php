<?php

use App\Http\Controllers\ContactMessageController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::post('/contact', [ContactMessageController::class, 'store'])
    ->middleware('throttle:contact-form')
    ->name('contact.store');

Route::post('/locale/{locale}', [LocaleController::class, 'update'])
    ->whereIn('locale', array_keys(config('app.supported_locales')))
    ->name('locale.update');
