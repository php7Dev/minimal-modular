<?php

namespace Modules\Core\Providers;

use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $modulePath = dirname(__DIR__);

        if (is_dir($modulePath.'/views')) {
            $this->loadViewsFrom($modulePath.'/views','core');
        }
    }
}
