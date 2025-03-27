<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChemicalController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/two', function () {
    return view('two');
});

Route::get('/mru-logo', function() {
    return response()->file(resource_path('views/MRU-logo.png'));
})->name('mru-logo');

Route::get('/chemicals/add', [ChemicalController::class, 'create'])->name('chemicals.create');
Route::post('/chemicals/add', [ChemicalController::class, 'store'])->name('chemicals.store');

Route::get('/chemicals/remove', [ChemicalController::class, 'deleteForm'])->name('chemicals.deleteForm');
Route::post('/chemicals/remove', [ChemicalController::class, 'destroy'])->name('chemicals.destroy');

Route::get('/chemicals/edit', [ChemicalController::class, 'editForm'])->name('chemicals.editForm');
Route::post('/chemicals/edit', [ChemicalController::class, 'update'])->name('chemicals.update');
