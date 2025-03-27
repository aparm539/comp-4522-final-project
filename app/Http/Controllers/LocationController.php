<?php

namespace App\Http\Controllers;

use App\Models\Container;
use App\Models\Location;
use App\Models\User;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $locations = Location::latest()->paginate(5);
        return view("locations.index", ['locations' => $locations]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::all();
        return view('locations.create', ['users' => $users]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Location::create([
            'room_number' => request('location_room_number'),
            'barcode' => random_int(0,1000000),
            'description' => request('location_description'),
            'supervisor_id' => request('user_id'),
        ]);
        return redirect('/locations');
    }

    /**
     * Display the specified resource.
     */
    public function show(Location $location)
    {
        $containers = Container::with(['location', 'unitofmeasure','shelf','chemical'])->where('location_id','=', $location->id)->paginate(5);
        return view('containers.index', ['containers' => $containers]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Location $location)
    {
        return view('locations.edit', ['location' => $location, 'users' => User::all()]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Location $location)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Location $location)
    {
        //
    }
}
