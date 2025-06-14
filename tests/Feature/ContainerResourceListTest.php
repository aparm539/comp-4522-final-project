<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;
use App\Filament\Resources\ContainerResource\Pages\ListContainers;
use App\Models\User;

class ContainerResourceListTest extends TestCase
{
    use RefreshDatabase;
    /** @test */
    public function container_resource_list_page_renders_successfully()
    {
        $this->actingAs(User::factory()->create());
        Livewire::test(ListContainers::class)
            ->assertSuccessful();
    }
}
