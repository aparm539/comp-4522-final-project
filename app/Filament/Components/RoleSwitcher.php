<?php

namespace App\Filament\Components;

use App\Models\Role;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Livewire\Component;

class RoleSwitcher extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'role' => strtolower($this->getCurrentRole())
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('role')
                    ->label('Select Role')
                    ->options([
                        'viewer' => 'Viewer',
                        'researcher' => 'Researcher',
                        'admin' => 'Admin',
                    ])
                    ->required(),
            ])
            ->statePath('data');
    }

    public function getCurrentRole(): string
    {
        $user = auth()->user();
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
        $data = $this->form->getState();
        $user = auth()->user();
        
        // Remove all existing roles
        $user->roles()->detach();
        
        // Assign the new role
        $role = Role::where('name', $data['role'])->first();
        if ($role) {
            $user->roles()->attach($role->id);
        }
        
        // Refresh the page to update the UI
        $this->redirect('/');
    }

    public function render()
    {
        return view('filament.components.role-switcher');
    }
} 