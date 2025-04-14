<?php

namespace App\Services;

use App\Models\Container;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;
use App\Models\Location;
use App\Models\StorageCabinet;

class ContainerService
{
    public function __construct(
        private Container $model,
    )
    {}

    public static function printContainers()
    {
        $containers = Container::all();
        return response()->streamDownload(function () use ($containers) {
            $pdf = PDF::loadView('containers.print', ['containers' => $containers]);
            echo $pdf->stream();
        }, 'containers.pdf');
    }

    public static function changeLocation(Collection $records, array $data)
    {
        $location = Location::find($data['location_id']);
        $storageCabinet = StorageCabinet::find($data['storage_cabinet_id']);
        $records->each(function ($record) use ($location, $storageCabinet) {
            $record->location_id = $location->id;
            $record->storage_cabinet_id = $storageCabinet->id;
            $record->save();
        });
    }
    public static function getUnavailableLocations()
    {
        return Location::whereHas('reconciliations', function ($query) {
            $query->where('status', 'ongoing');
        })->get();
    }
}
