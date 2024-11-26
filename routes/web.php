<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MainController;
use App\Http\Controllers\CityController;

Route::get('/reset', function () {
    session()->forget('city');
    return redirect()->route('index');
})->name('reset');

Route::prefix(\App\Helpers\CitySlug::getSlug())->middleware('city')->group(function () {
    Route::get('/', [MainController::class, 'index'])->name('index');
    Route::get('/about', [MainController::class, 'about'])->name('about');
    Route::get('/news', [MainController::class, 'news'])->name('news');
});

/*Route::prefix('{city?}')->group(function () {
    Route::get('/', [MainController::class, 'index'])->name('index');
    Route::get('/about', [MainController::class, 'about'])->name('about');
    Route::get('/news', [MainController::class, 'news'])->name('news');
});*/

Route::get('/get-countries-capital', [CityController::class, 'getCountriesCapital']);
Route::get('/get-cities', [CityController::class, 'getCities']);
