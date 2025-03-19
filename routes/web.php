<?php

use App\Models\Location;
use App\Models\Shelf;
use Illuminate\Support\Facades\Route;
use App\Models\Container;

Route::get('/', function () {
    return view("home");
});

Route::get('/containers', function () {
    $containers = Container::with(['location', 'unitofmeasure','shelf'])->paginate(5);
    return view("containers", ['containers' => $containers]);

});

Route::get('/locations/{id}', function ($id) {
    $containers = Container::with(['location', 'unitofmeasure','shelf'])->where('location_id','=', $id)->paginate(5);
    return view("containers", ['containers' => $containers]);
});


Route::get('/locations', function () {
    $locations = Location::paginate(15);
    return view("locations", ['locations' => $locations]);
});
