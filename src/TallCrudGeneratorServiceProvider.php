<?php

namespace Ascsoftw\TallCrudGenerator;

use Ascsoftw\TallCrudGenerator\Console\Commands\TallCrudGeneratorCommand;
use Ascsoftw\TallCrudGenerator\Http\GenerateCode\ChildComponentCode;
use Ascsoftw\TallCrudGenerator\Http\GenerateCode\ChildViewCode;
use Ascsoftw\TallCrudGenerator\Http\GenerateCode\ComponentCode;
use Ascsoftw\TallCrudGenerator\Http\GenerateCode\TallProperties;
use Ascsoftw\TallCrudGenerator\Http\GenerateCode\ViewCode;
use Ascsoftw\TallCrudGenerator\Http\Livewire\TallCrudGenerator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class TallCrudGeneratorServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Livewire::component('tall-crud-generator', TallCrudGenerator::class);
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'tall-crud-generator');

        $this->commands([
            TallCrudGeneratorCommand::class,
        ]);

        $this->configureComponents();
        $this->defineMacros();

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/tall-crud-generator'),
        ], 'views');
    }

    public function register()
    {
        $this->app->singleton(TallProperties::class, function ($app) {
            return new TallProperties();
        });

        $this->app->singleton(ComponentCode::class, function ($app) {
            return new ComponentCode($app->make(TallProperties::class));
        });

        $this->app->singleton(ChildComponentCode::class, function ($app) {
            return new ChildComponentCode($app->make(TallProperties::class));
        });

        $this->app->singleton(ViewCode::class, function ($app) {
            return new ViewCode($app->make(TallProperties::class));
        });

        $this->app->singleton(ChildViewCode::class, function ($app) {
            return new ChildViewCode($app->make(TallProperties::class));
        });
    }

    public function configureComponents()
    {
        Blade::component('tall-crud-generator::components.label', 'tall-crud-label');
        Blade::component('tall-crud-generator::components.button', 'tall-crud-button');
        Blade::component('tall-crud-generator::components.checkbox-wrapper', 'tall-crud-checkbox-wrapper');
        Blade::component('tall-crud-generator::components.checkbox', 'tall-crud-checkbox');
        Blade::component('tall-crud-generator::components.confirmation-dialog', 'tall-crud-confirmation-dialog');
        Blade::component('tall-crud-generator::components.dialog-modal', 'tall-crud-dialog-modal');
        Blade::component('tall-crud-generator::components.error-message', 'tall-crud-error-message');
        Blade::component('tall-crud-generator::components.h2', 'tall-crud-h2');
        Blade::component('tall-crud-generator::components.input', 'tall-crud-input');
        Blade::component('tall-crud-generator::components.modal', 'tall-crud-modal');
        Blade::component('tall-crud-generator::components.select', 'tall-crud-select');
        Blade::component('tall-crud-generator::components.sort-icon', 'tall-crud-sort-icon');
        Blade::component('tall-crud-generator::components.table-column', 'tall-crud-table-column');
        Blade::component('tall-crud-generator::components.table', 'tall-crud-table');
        Blade::component('tall-crud-generator::components.tag', 'tall-crud-tag');
        Blade::component('tall-crud-generator::components.accordion-header', 'tall-crud-accordion-header');
        Blade::component('tall-crud-generator::components.accordion-wrapper', 'tall-crud-accordion-wrapper');
        Blade::component('tall-crud-generator::components.wizard-step', 'tall-crud-wizard-step');
        Blade::component('tall-crud-generator::components.loading-indicator', 'tall-crud-loading-indicator');
        Blade::component('tall-crud-generator::components.show-relations-table', 'tall-crud-show-relations-table');
        Blade::component('tall-crud-generator::components.dropdown', 'tall-crud-dropdown');
        Blade::component('tall-crud-generator::components.tooltip', 'tall-crud-tooltip');
        Blade::component('tall-crud-generator::components.icon-filter', 'tall-crud-icon-filter');
        Blade::component('tall-crud-generator::components.icon-add', 'tall-crud-icon-add');
        Blade::component('tall-crud-generator::components.icon-edit', 'tall-crud-icon-edit');
        Blade::component('tall-crud-generator::components.icon-delete', 'tall-crud-icon-delete');
        Blade::component('tall-crud-generator::components.filter', 'tall-crud-filter');
        Blade::component('tall-crud-generator::components.input-search', 'tall-crud-input-search');
        Blade::component('tall-crud-generator::components.columns-dropdown', 'tall-crud-columns-dropdown');
        Blade::component('tall-crud-generator::components.page-dropdown', 'tall-crud-page-dropdown');
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
