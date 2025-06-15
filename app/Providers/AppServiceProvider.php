<?php

namespace App\Providers;

use App\Models\Container;
use App\Services\Container\ContainerService;
use App\Services\Container\PdfGeneratorService;
use Filament\Support\Facades\FilamentView;
use Filament\Tables\View\TablesRenderHook;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Psr\Log\LoggerInterface;

class AppServiceProvider extends ServiceProvider
{
    private \Illuminate\Contracts\Database\Query\Expression $from;

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ContainerService::class, function ($app) {
            return new ContainerService(
                $app->make(Container::class),
                $app->make(LoggerInterface::class),
                $app->make(PdfGeneratorService::class),
            );
        });

        // Register a render hook to persistently show disabled bulk-action placeholder buttons
        // in the Chemicals table toolbar until the user selects records.
        FilamentView::registerRenderHook(
            TablesRenderHook::TOOLBAR_START,
            /**
             * @param  array<string>  $scopes  The component classes that invoked the hook.
             */
            function (array $scopes): string {
                $buttons = [];

                // Determine the Table class related to the current ListRecords / Livewire page.
                $pageClass = $scopes[0] ?? null;

                if (is_string($pageClass) && method_exists($pageClass, 'getResource')) {
                    /** @var class-string<\Filament\Resources\Resource>|null $resourceClass */
                    $resourceClass = $pageClass::getResource();

                    if ($resourceClass) {
                        // Attempt to infer the Table class - by default {ResourceNamespace}\{ResourceName}Table
                        $resourceReflection = new \ReflectionClass($resourceClass);
                        $resourceNamespace = $resourceReflection->getNamespaceName();
                        $resourceShortName = $resourceReflection->getShortName(); // e.g., ChemicalResource

                        $base = str_replace('Resource', '', $resourceShortName); // Chemical
                        $tableClass = $resourceNamespace.'\\'.$base.'Table';

                        if (class_exists($tableClass) && method_exists($tableClass, 'bulkActions')) {
                            $custom = collect($tableClass::bulkActions());
                            $buttons = $custom
                                ->map(function ($action) {
                                    if (! method_exists($action, 'getLabel')) {
                                        // Skip items like ActionGroup etc.
                                        return null;
                                    }

                                    return [
                                        'label' => $action->getLabel(),
                                        'color' => $action->getColor() ?? 'gray',
                                        'icon' => $action->getIcon(),
                                    ];
                                })
                                ->filter()
                                ->values()
                                ->toArray();
                        }
                    }
                }

                if (empty($buttons)) {
                    // No placeholder buttons configured for this page—render nothing.
                    return '';
                }

                return view('filament.tables.disabled-bulk-buttons', [
                    'buttons' => $buttons,
                ])->render();
            },
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::unguard();
        \Illuminate\Database\Query\Builder::macro('bySystemTime', function (?string $timestamp = null) {
            $timestamp = $timestamp ? "AS OF TIMESTAMP '$timestamp'" : 'ALL';
            $this->from = DB::raw("`$this->from` FOR SYSTEM_TIME $timestamp");

            return $this;
        });
    }
}
