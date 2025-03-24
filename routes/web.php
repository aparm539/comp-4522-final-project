<?php

use App\Http\Controllers\ContainerController;
use App\Models\Chemical;
use App\Models\Location;
use App\Models\Shelf;
use App\Models\UnitOfMeasure;
use Illuminate\Support\Facades\Route;
use App\Models\Container;

Route::view('/', 'home');
Route::resource('containers', ContainerController::class);



Route::get('/locations/{id}', function ($id) {
    $containers = Container::with(['location', 'unitofmeasure','shelf','chemical'])->where('location_id','=', $id)->paginate(5);
    return view('containers.index', ['containers' => $containers]);
});


Route::get('/locations', function () {
    $locations = Location::paginate(15);
    return view("locations", ['locations' => $locations]);
});
