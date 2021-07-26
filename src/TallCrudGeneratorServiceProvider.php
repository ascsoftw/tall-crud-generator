<?php

namespace Ascsoftw\TallCrudGenerator;

use Ascsoftw\TallCrudGenerator\Console\Commands\TallCrudGeneratorCommand;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Ascsoftw\TallCrudGenerator\Http\Livewire\TallCrudGenerator;
use Illuminate\Support\Facades\Blade;
use Livewire\Livewire;

class TallCrudGeneratorServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Livewire::component('tall-crud-generator', TallCrudGenerator::class);
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'tall-crud-generator');

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
        Blade::component('tall-crud-generator::sort-fields-table', 'tall-crud-sort-fields-table');
        Blade::component('tall-crud-generator::loading-indicator', 'tall-crud-loading-indicator');
        Blade::component('tall-crud-generator::show-relations-table', 'tall-crud-show-relations-table');

        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/tall-crud-generator')
        ], 'views');

        $this->publishes([
            __DIR__ . '/../config/tall-crud-generator.php' => base_path('config/tall-crud-generator.php')
        ], 'config');

        if (!config('tall-crud-generator.disable_route')) {
            Route::get(config('tall-crud-generator.route'), function () {
                return view('tall-crud-generator::tall-crud-generator');
            })->middleware(['web', 'auth']);
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/tall-crud-generator.php', 'tall-crud-generator');
    }
}
