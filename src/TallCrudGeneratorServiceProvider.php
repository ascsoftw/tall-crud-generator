<?php

namespace Ascsoftw\TallCrudGenerator;

use Ascsoftw\TallCrudGenerator\Console\Commands\TallCrudGeneratorCommand;
use Ascsoftw\TallCrudGenerator\Http\GenerateCode\ComponentCode;
use Ascsoftw\TallCrudGenerator\Http\GenerateCode\ChildComponentCode;
use Ascsoftw\TallCrudGenerator\Http\GenerateCode\ViewCode;
use Ascsoftw\TallCrudGenerator\Http\GenerateCode\ChildViewCode;
use Ascsoftw\TallCrudGenerator\Http\Livewire\TallCrudGenerator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Ascsoftw\TallCrudGenerator\Http\Livewire\TallProperties;

class TallCrudGeneratorServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Livewire::component('tall-crud-generator', TallCrudGenerator::class);
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'tall-crud-generator');

        $this->commands([
            TallCrudGeneratorCommand::class,
        ]);

        Blade::component('tall-crud-generator::label', 'tall-crud-label');
        Blade::component('tall-crud-generator::button', 'tall-crud-button');
        Blade::component('tall-crud-generator::checkbox-wrapper', 'tall-crud-checkbox-wrapper');
        Blade::component('tall-crud-generator::checkbox', 'tall-crud-checkbox');
        Blade::component('tall-crud-generator::confirmation-dialog', 'tall-crud-confirmation-dialog');
        Blade::component('tall-crud-generator::dialog-modal', 'tall-crud-dialog-modal');
        Blade::component('tall-crud-generator::error-message', 'tall-crud-error-message');
        Blade::component('tall-crud-generator::h2', 'tall-crud-h2');
        Blade::component('tall-crud-generator::input', 'tall-crud-input');
        Blade::component('tall-crud-generator::modal', 'tall-crud-modal');
        Blade::component('tall-crud-generator::select', 'tall-crud-select');
        Blade::component('tall-crud-generator::sort-icon', 'tall-crud-sort-icon');
        Blade::component('tall-crud-generator::table-column', 'tall-crud-table-column');
        Blade::component('tall-crud-generator::table', 'tall-crud-table');
        Blade::component('tall-crud-generator::tag', 'tall-crud-tag');
        Blade::component('tall-crud-generator::accordion-heading', 'tall-crud-accordion-heading');
        Blade::component('tall-crud-generator::accordion-wrapper', 'tall-crud-accordion-wrapper');
        Blade::component('tall-crud-generator::wizard-step', 'tall-crud-wizard-step');
        Blade::component('tall-crud-generator::loading-indicator', 'tall-crud-loading-indicator');
        Blade::component('tall-crud-generator::show-relations-table', 'tall-crud-show-relations-table');
        Blade::component('tall-crud-generator::dropdown', 'tall-crud-dropdown');
        Blade::component('tall-crud-generator::tooltip', 'tall-crud-tooltip');
        Blade::component('tall-crud-generator::filter-icon', 'tall-crud-filter-icon');

        $this->defineMacros();

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/tall-crud-generator'),
        ], 'views');

        $this->publishes([
            __DIR__.'/../config/tall-crud-generator.php' => base_path('config/tall-crud-generator.php'),
        ], 'config');

        if (! config('tall-crud-generator.disable_route')) {
            Route::get(config('tall-crud-generator.route'), function () {
                return view('tall-crud-generator::tall-crud-generator');
            })->middleware(['web', 'auth']);
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/tall-crud-generator.php', 'tall-crud-generator');
        $this->app->singleton(TallProperties::class, function ($app) {
            return new TallProperties();
        });

        $this->app->bind(ComponentCode::class, function ($app) {
            return new ComponentCode($app->make(TallProperties::class));
        });

        $this->app->bind(ChildComponentCode::class, function ($app) {
            return new ChildComponentCode($app->make(TallProperties::class));
        });

        $this->app->bind(ViewCode::class, function ($app) {
            return new ViewCode($app->make(TallProperties::class));
        });

        $this->app->bind(ChildViewCode::class, function ($app) {
            return new ChildViewCode($app->make(TallProperties::class));
        });
    }

    public function defineMacros()
    {
        Collection::macro('prependAndJoin', function ($glue, $prepend = '', $lastGlue = '') {
            if (empty($prepend)) {
                $prepend = $glue;
            }

            return $prepend.$this->join($glue, $lastGlue);
        });
    }
}
