<?php

use App\Http\Controllers\ResultsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome', [
        'catalog' => require resource_path('data/klimaatmonitor_catalog.php'),
    ]);
});

Route::get('/resultaten', [ResultsController::class, 'index'])->name('results.index');
Route::get('/resultaten/download/csv', [ResultsController::class, 'csv'])->name('results.csv');
Route::get('/resultaten/download/excel', [ResultsController::class, 'excel'])->name('results.excel');
