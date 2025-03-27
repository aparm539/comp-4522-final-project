<?php

use App\Http\Controllers\ContainerController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserSearchController;
use App\Models\Chemical;
use App\Models\Location;
use App\Models\Shelf;
use App\Models\UnitOfMeasure;
use Illuminate\Support\Facades\Route;
use App\Models\Container;

Route::get('users/search', [UserController::class, 'search'])->name('users.search');
Route::view('/', 'home');
Route::resource('containers', ContainerController::class);
Route::resource('locations', LocationController::class);

Route::resource('users', UserController::class,);

