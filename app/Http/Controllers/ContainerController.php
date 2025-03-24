<?php

namespace App\Http\Controllers;

use App\Models\Chemical;
use App\Models\Container;
use App\Models\Location;
use App\Models\Shelf;
use App\Models\UnitOfMeasure;
use Illuminate\Http\Request;

class ContainerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $containers = Container::with(['location', 'unitofmeasure','shelf','chemical'])->latest()->paginate(5);
        return view("containers.index", ['containers' => $containers]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $locations = Location::all();
        $shelves = Shelf::all();
        $chemicals = Chemical::all();
        $units = UnitOfMeasure::all();
        return view('containers.create',['locations' => $locations, 'shelves' => $shelves, 'chemicals' => $chemicals, 'units' => $units]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Container::create([
            'barcode' => random_int(0,1000000),
            'quantity' => request('amount'),
            'unit_of_measure' => request('unit'),
            'chemical_id' => request('cas'),
            'location_id' => request('location'),
            'shelf_id' => '1',
            'ishazardous' => 'true',
            'supervisor_id' => '1',
        ]);
        return redirect('/containers');
    }

    /**
     * Display the specified resource.
     */
    public function show(Container $container)
    {
        return view('containers.show', ['container' => $container]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Container $container)
    {
        $locations = Location::all();
        $shelves = Shelf::all();
        $chemicals = Chemical::all();
        $units = UnitOfMeasure::all();
        return view('containers.edit', ['container' => $container,'locations' => $locations, 'shelves' => $shelves, 'chemicals' => $chemicals, 'units' => $units]);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Container $container)
    {
        // Validate (TODO)
        // Authorize (TODO)
        $container->update([
            'barcode' => $container->barcode,
            'quantity' => request('amount'),
            'unit_of_measure' => request('unit'),
            'chemical_id' => request('cas'),
            'location_id' => request('location'),
            'shelf_id' => '1',
            'ishazardous' => 'true',
            'supervisor_id' => '1',
        ]);
        // TODO: Handle case where ID is not found
        return redirect('/containers/' . $container->id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Container $container)
    {
        // Authorize
        $container->delete();
        return redirect('/containers');
    }
}
