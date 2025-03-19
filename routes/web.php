<?php

use Illuminate\Support\Facades\Route;
use App\Models\Container;

Route::get('/', function () {
    return view("home");
});

Route::get('/containers', function () {
    $containers = Container::with('location')->paginate(5);
    return view("containers", ['containers' => $containers]);
});
