<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\CastingController;
use App\Livewire\Portfolio\PortfolioIndex;
use App\Livewire\Portfolio\PortfolioShow;
use App\Livewire\Casting\CastingIndex;
use App\Livewire\Casting\CastingShow;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    Route::get('/portfolios/create', [PortfolioController::class, 'create'])->name('portfolios.create');
    Route::post('/portfolios', [PortfolioController::class, 'store'])->name('portfolios.store');
    Route::get('/portfolios/{portfolio}/edit', [PortfolioController::class, 'edit'])->name('portfolios.edit');
    Route::put('/portfolios/{portfolio}', [PortfolioController::class, 'update'])->name('portfolios.update');
    Route::delete('/portfolios/{portfolio}', [PortfolioController::class, 'destroy'])->name('portfolios.destroy');
    
    Route::get('/castings/create', [CastingController::class, 'create'])->name('castings.create');
    Route::post('/castings', [CastingController::class, 'store'])->name('castings.store');
    Route::get('/castings/{casting}/edit', [CastingController::class, 'edit'])->name('castings.edit');
    Route::put('/castings/{casting}', [CastingController::class, 'update'])->name('castings.update');
    Route::delete('/castings/{casting}', [CastingController::class, 'destroy'])->name('castings.destroy');
});

Route::get('/portfolios', PortfolioIndex::class)->name('portfolios.index');
Route::get('/portfolios/{portfolio}', PortfolioShow::class)->name('portfolios.show');

Route::get('/castings', CastingIndex::class)->name('castings.index');
Route::get('/castings/{casting}', CastingShow::class)->name('castings.show');

require __DIR__.'/auth.php';


