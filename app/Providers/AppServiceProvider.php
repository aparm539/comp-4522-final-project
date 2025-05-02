<?php

namespace App\Providers;

use App\Models\Container;
use App\Services\Container\ContainerService;
use App\Services\Container\PdfGeneratorService;
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

        //
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
