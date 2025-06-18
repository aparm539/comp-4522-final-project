<?php

use App\Filament\Resources\ContainerResource\Pages\ListContainers;
use App\Models\User;
use Livewire\Livewire;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('container resource list page renders successfully', function () {
    $this->actingAs(User::factory()->create());
    Livewire::test(ListContainers::class)
        ->assertSuccessful();
});