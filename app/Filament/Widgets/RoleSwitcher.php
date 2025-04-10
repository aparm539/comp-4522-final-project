<?php

namespace App\Filament\Widgets;

use App\Models\Role;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;
use Illuminate\Contracts\View\View;

class RoleSwitcher extends Widget
{
    use WithPagination;

    protected static string $view = 'filament.widgets.role-switcher';

    protected int | string | array $columnSpan = 'full';

    public ?string $selectedRole = null;

    public function mount(): void
    {
        $this->selectedRole = strtolower($this->getCurrentRole());
    }

    public function getCurrentRole(): string
    {
        $user = Auth::user();
        if ($user->isAdmin()) {
            return 'Admin';
        }
        if ($user->isResearcher()) {
            return 'Researcher';
        }
        return 'Viewer';
    }

    public function changeRole(): void
    {
        $user = Auth::user();
        
        // Remove all existing roles
        $user->roles()->detach();
        
        // Assign the new role
        $role = Role::where('name', $this->selectedRole)->first();
        if ($role) {
            $user->roles()->attach($role->id);
        }
        
        // Refresh the page to update the UI
        $this->redirect('/');
    }

    public function render(): View
    {
        return view(static::$view, [
            'selectedRole' => $this->selectedRole,
        ]);
    }
} 