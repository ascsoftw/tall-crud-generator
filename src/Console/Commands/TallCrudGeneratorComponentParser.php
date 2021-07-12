<?php

namespace Ascsoftw\TallCrudGenerator\Console\Commands;

use Livewire\Commands\ComponentParser;
use Illuminate\Support\Facades\File;

class TallCrudGeneratorComponentParser extends ComponentParser
{

    public function classContents($child = false, $props = [])
    {
        $stubName = $child ? 'tall-crud.child.stub' : 'tall-crud.stub';

        if (File::exists($stubPath = base_path('stubs/' . $stubName))) {
            $template = file_get_contents($stubPath);
        } else {
            $template = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . $stubName);
        }

        if (!$child) {
            $template = preg_replace_array(
                [
                    '/\[namespace\]/',
                    '/\[class\]/',
                    '/\[view\]/',
                    '/\[modelPath\]/',
                    '/\[model\]/',
                    '/\[sort_public_vars\]/',
                    '/\[sort_query\]/',
                    '/\[sort_method\]/',
                    '/\[search_query\]/',
                    '/\[search_vars\]/',
                    '/\[search_method\]/',
                    '/\[pagination_dropdown_method\]/',
                    '/\[pagination_vars\]/',
                ],
                [
                    $this->classNamespace(),
                    $this->className(),
                    $this->viewName(),
                    $props['modelPath'],
                    $props['model'],
                    $props['code']['sort']['vars'],
                    $props['code']['sort']['query'],
                    $props['code']['sort']['method'],
                    $props['code']['search']['query'],
                    $props['code']['search']['vars'],
                    $props['code']['search']['method'],
                    $props['code']['pagination_dropdown']['method'],
                    $props['code']['pagination']['vars'],
                ],
                $template
            );
        } else {
            //Replace Child Params here.
            $template = preg_replace_array(
                [
                    '/\[namespace\]/',
                    '/\[class\]/',
                    '/\[view\]/',
                    '/\[modelPath\]/',
                    '/\[child_delete_vars\]/',
                    '/\[child_delete_method\]/',
                    '/\[child_listeners\]/',
                    '/\[child_item\]/',
                    '/\[child_rules\]/',
                    '/\[child_add_vars\]/',
                    '/\[child_edit_vars\]/',
                    '/\[child_add_method\]/',
                    '/\[child_edit_method\]/',
                    '/\[child_validation_attributes\]/',
                    '/\[child_other_models\]/',
                    '/\[child_vars\]/',
                ],
                [
                    $this->classNamespace(),
                    $this->className(),
                    $this->viewName(),
                    $props['modelPath'],
                    $props['code']['child_delete']['vars'],
                    $props['code']['child_delete']['method'],
                    $props['code']['child_listeners'],
                    $props['code']['child_item'],
                    $props['code']['child_rules'],
                    $props['code']['child_add']['vars'],
                    $props['code']['child_edit']['vars'],
                    $props['code']['child_add']['method'],
                    $props['code']['child_edit']['method'],
                    $props['code']['child_validation_attributes'],
                    $props['code']['child_other_models'],
                    $props['code']['child_vars'],
                ],
                $template
            );
        }

        return $template;
    }

    public function viewContents($child = false, $props = [])
    {

        $stubName = $child ? 'tall-crud.child.view.stub' : 'tall-crud.view.stub';

        if (File::exists($stubPath = base_path('stubs/' . $stubName))) {
            $template = file_get_contents($stubPath);
        } else {
            $template = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . $stubName);
        }

        if (!$child) {
            $template =  preg_replace_array(
                [
                    '/\[heading\]/',
                    '/\[css_class\]/',
                    '/\[add_link\]/',
                    '/\[search_box\]/',
                    '/\[table_header\]/',
                    '/\[table_slot\]/',
                    '/\[child_component\]/',
                    '/\[flash_component\]/',
                    '/\[pagination_dropdown\]/',
                ],
                [
                    $props['advancedSettings']['title'],
                    $props['html']['css_class'],
                    $props['html']['add_link'],
                    $props['html']['search_box'],
                    $props['html']['table_header'],
                    $props['html']['table_slot'],
                    $props['html']['child_component'],
                    $props['html']['flash_component'],
                    $props['html']['pagination_dropdown'],
                ],
                $template
            );
        } else {
            $template =  preg_replace_array(
                [
                    '/\[delete_modal\]/',
                    '/\[add_modal\]/',
                    '/\[edit_modal\]/',
                ],
                [
                    $props['html']['child']['delete_modal'],
                    $props['html']['child']['add_modal'],
                    $props['html']['child']['edit_modal'],
                ],
                $template
            );
        }

        return $template;
    }
}
