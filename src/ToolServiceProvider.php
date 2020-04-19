<?php

namespace Armincms\Template;
 
use Illuminate\Support\ServiceProvider; 
use Laravel\Nova\Nova as LaravelNova; 

class ToolServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'armin.template');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'armin.template'); 
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'armin.template'); 
        $this->mergeConfigFrom(__DIR__.'/../config/icon.php', 'armin.icon');
        $this->commands(Console\TemplateMakeCommand::class);    
        LaravelNova::serving([$this, 'servingNova']);
    }
    
    public function servingNova()
    {
        LaravelNova::resources([
            Nova\Template::class,
        ]);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {  
        $this->app->bind('template', function($app) {
            return new Factory($app['template.repository']);
        });

        $this->app->bind('template.repository', function($app) {
            return new Repository($app['files']);
        }); 
    }
}
