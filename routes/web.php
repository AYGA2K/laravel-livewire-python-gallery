<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\ProcessingImage;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('upload', 'upload')
    ->middleware(['auth', 'verified'])
    ->name('upload');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::get('/process/{imageId}', ProcessingImage::class)
    ->middleware(['auth'])
    ->name('process-image');



require __DIR__ . '/auth.php';
