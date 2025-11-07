<?php

namespace App\Filament\Resources\ReconciliationResource\Pages;

use App\Filament\Resources\ReconciliationResource\ReconciliationResource;
use Filament\Actions;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewReconciliation extends ViewRecord
{
    protected static string $resource = ReconciliationResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Reconciliation Details')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        TextEntry::make('lab.room_number')
                            ->label('Lab')
                            ->icon('heroicon-o-building-office'),
                        TextEntry::make('status')
                            ->label('Status')
                            ->formatStateUsing(fn (string $state): string => ucfirst($state))
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'stopped' => 'danger',
                                'ongoing' => 'warning',
                                'completed' => 'success',
                                default => 'gray',
                            })
                            ->icon(fn (string $state): string => match ($state) {
                                'stopped' => 'heroicon-o-no-symbol',
                                'ongoing' => 'heroicon-o-pencil',
                                'completed' => 'heroicon-o-check-circle',
                                default => 'heroicon-o-question-mark-circle',
                            }),
                        TextEntry::make('started_at')
                            ->label('Started At')
                            ->dateTime()
                            ->icon('heroicon-o-clock')
                            ->placeholder('Not started'),
                        TextEntry::make('ended_at')
                            ->label('Ended At')
                            ->dateTime()
                            ->icon('heroicon-o-clock')
                            ->placeholder('Not ended'),
                        TextEntry::make('notes')
                            ->label('Notes')
                            ->icon('heroicon-o-document-text')
                            ->placeholder('No notes')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->compact(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->slideOver(),
        ];
    }
}
