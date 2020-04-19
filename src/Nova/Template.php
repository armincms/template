<?php

namespace Armincms\Template\Nova;

use Laravel\Nova\Panel; 
use Laravel\Nova\Fields\Text; 
use Laravel\Nova\Fields\Code; 
use Laravel\Nova\Fields\Select;  
use OptimistDigital\MultiselectField\Multiselect;  
use Illuminate\Http\Request; 
use Armincms\Nova\ConfigResource; 

class Template extends ConfigResource
{     
    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name'; 

    /**
     * The logical group associated with the resource.
     *
     * @var string
     */
    public static $group = '';

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        $templates = app('template.repository')->all();

        return [
            Select::make(__('Default Template'), '_default_template_')
                ->options($templates->map->label())
                ->withMeta([
                    'value' => $templates->keys()->first()
                ]), 

            new Panel(__('Customization'), $this->tab(function($tab) use ($templates) {
                $templates->each(function($template, $name) use ($tab) {
                    $tab->group($name, [
                        Multiselect::make(__('Required Plugins'), "_{$name}_plugins_")
                            ->options(armin_plugins()->map(function($plugin) {
                                return [
                                    $plugin->name() => $plugin->label()
                                ];
                            })->push('a')->push('asd'))
                            ->saveAsJSON() ,

                        Code::make(__('Custom Css'), "_{$name}_custom_css_")
                            ->language('css')
                            ->fillUsing(function($request, $model, $attribute) use ($name) {
                                $model->{$attribute} = $request->get($attribute);


                                \File::put(
                                    template_path("{$name}/custom.css"), $model->{$attribute}
                                );
                                \File::put(
                                    template_path("{$name}/custom.min.css"), minify_css($model->{$attribute})
                                );
                            }),
                    ]);
                });
            })->toArray()),
        ];
    } 
}
