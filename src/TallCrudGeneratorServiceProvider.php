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

        Blade::component('tall-crud-generator::label', 'label');
        Blade::component('tall-crud-generator::button', 'button');
        Blade::component('tall-crud-generator::checkbox-wrapper', 'xcheckbox-wrapper');
        Blade::component('tall-crud-generator::checkbox', 'checkbox');
        Blade::component('tall-crud-generator::confirmation-dialog', 'confirmation-dialog');
        Blade::component('tall-crud-generator::dialog-modal', 'dialog-modal');
        Blade::component('tall-crud-generator::error-message', 'error-message');
        Blade::component('tall-crud-generator::h2', 'h2');
        Blade::component('tall-crud-generator::input', 'input');
        Blade::component('tall-crud-generator::modal', 'modal');
        Blade::component('tall-crud-generator::select', 'select');
        Blade::component('tall-crud-generator::sort-icon', 'sort-icon');
        Blade::component('tall-crud-generator::table-column', 'table-column');
        Blade::component('tall-crud-generator::table', 'table');
        Blade::component('tall-crud-generator::tag', 'tag');

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
