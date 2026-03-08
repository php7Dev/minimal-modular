<?php

namespace Modules\Blog\Providers;

use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $modulePath = dirname(__DIR__);

        if (file_exists($modulePath.'/routes/web.php')) {
            $this->loadRoutesFrom($modulePath.'/routes/web.php');
        }

        if (is_dir($modulePath.'/views')) {
            $this->loadViewsFrom($modulePath.'/views','blog');
        }

        if (is_dir($modulePath.'/migrations')) {
            $this->loadMigrationsFrom($modulePath.'/migrations');
        }
    }

    public function register()
    {
        $modulePath = dirname(__DIR__);

        if (file_exists($modulePath.'/config/config.php')) {
            $this->mergeConfigFrom(
                $modulePath.'/config/config.php',
                'blog'
            );
        }
    }
}
