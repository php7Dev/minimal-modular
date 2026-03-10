<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ModuleMakeAll extends Command
{
    protected $signature = 'module:make-all {module} {name} {layout?}';
    protected $description = 'Create a full module with controller, model, middleware, service and optional layout module';

    public function handle()
    {
        $module = ucfirst($this->argument('module'));
        $name   = ucfirst($this->argument('name'));
        $layout = $this->argument('layout');

        $modulePath = base_path("Modules/$module");

        /*
        |--------------------------------
        | Create Module Folders
        |--------------------------------
        */

        $folders = [
            "Controllers",
            "Models",
            "Middleware",
            "Providers",
            "routes",
            "views",
            "config",
            "migrations",
            "Services"
        ];

        foreach ($folders as $folder) {
            @mkdir("$modulePath/$folder", 0755, true);
        }

        /*
        |--------------------------------
        | module.json
        |--------------------------------
        */

        file_put_contents(
            "$modulePath/module.json",
            json_encode([
                "name"=>$module,
                "enabled"=>true,
                "provider"=>"Modules\\$module\\Providers\\ModuleServiceProvider"
            ], JSON_PRETTY_PRINT)
        );

        /*
        |--------------------------------
        | Config
        |--------------------------------
        */

        file_put_contents(
            "$modulePath/config/config.php",
            "<?php return ['route_prefix'=>'".strtolower($module)."'];"
        );

        /*
        |--------------------------------
        | Service Provider
        |--------------------------------
        */

        file_put_contents(
"$modulePath/Providers/ModuleServiceProvider.php",
"<?php

namespace Modules\\$module\\Providers;

use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    public function boot()
    {
        \$modulePath = dirname(__DIR__);

        if (file_exists(\$modulePath.'/routes/web.php')) {
            \$this->loadRoutesFrom(\$modulePath.'/routes/web.php');
        }

        if (is_dir(\$modulePath.'/views')) {
            \$this->loadViewsFrom(\$modulePath.'/views','".strtolower($module)."');
        }

        if (is_dir(\$modulePath.'/migrations')) {
            \$this->loadMigrationsFrom(\$modulePath.'/migrations');
        }
    }

    public function register()
    {
        \$modulePath = dirname(__DIR__);

        if (file_exists(\$modulePath.'/config/config.php')) {
            \$this->mergeConfigFrom(
                \$modulePath.'/config/config.php',
                '".strtolower($module)."'
            );
        }
    }
}
");

        /*
        |--------------------------------
        | Controller
        |--------------------------------
        */

        file_put_contents(
"$modulePath/Controllers/{$name}Controller.php",
"<?php

namespace Modules\\$module\\Controllers;

use App\Http\Controllers\Controller;

class {$name}Controller extends Controller
{
    public function index()
    {
        return view('".strtolower($module)."::".strtolower($name)."');
    }
}
");

        /*
        |--------------------------------
        | Model
        |--------------------------------
        */

        file_put_contents(
"$modulePath/Models/$name.php",
"<?php

namespace Modules\\$module\\Models;

use Illuminate\Database\Eloquent\Model;

class $name extends Model
{
    protected \$guarded = [];
}
");

        /*
        |--------------------------------
        | Middleware
        |--------------------------------
        */

        file_put_contents(
"$modulePath/Middleware/{$name}Middleware.php",
"<?php

namespace Modules\\$module\\Middleware;

use Closure;
use Illuminate\Http\Request;

class {$name}Middleware
{
    public function handle(Request \$request, Closure \$next)
    {
        return \$next(\$request);
    }
}
");

        /*
        |--------------------------------
        | Service Class
        |--------------------------------
        */

        file_put_contents(
"$modulePath/Services/{$name}Service.php",
"<?php

namespace Modules\\$module\\Services;

use Modules\\$module\\Models\\$name;

class {$name}Service
{
    protected \$$name;

    public function __construct($name \$$name)
    {
        \$this->$name = \$$name;
    }

    public function all()
    {
        return \$this->{$name}::all();
    }
}
");

        /*
        |--------------------------------
        | Routes
        |--------------------------------
        */

        file_put_contents(
"$modulePath/routes/web.php",
"<?php

use Illuminate\Support\Facades\Route;
use Modules\\$module\\Controllers\\{$name}Controller;

Route::get('/".strtolower($module)."', [{$name}Controller::class,'index']);
");

        /*
        |--------------------------------
        | Layout Module (Optional)
        |--------------------------------
        */

        if ($layout) {

            $layoutModule = ucfirst($layout);
            $layoutPath = base_path("Modules/$layoutModule");

            @mkdir("$layoutPath/views/layouts",0755,true);
            @mkdir("$layoutPath/Providers",0755,true);

            /*
            | module.json
            */

            if (!file_exists("$layoutPath/module.json")) {

                file_put_contents(
                    "$layoutPath/module.json",
                    json_encode([
                        "name"=>$layoutModule,
                        "enabled"=>true,
                        "provider"=>"Modules\\$layoutModule\\Providers\\ModuleServiceProvider"
                    ], JSON_PRETTY_PRINT)
                );
            }

            /*
            | Layout View
            */

            if (!file_exists("$layoutPath/views/layouts/app.blade.php")) {

                file_put_contents(
"$layoutPath/views/layouts/app.blade.php",
"<!DOCTYPE html>
<html>
<head>
<title>@yield('title')</title>
</head>

<body>

<header>
<h2>$layoutModule Layout</h2>
</header>

<main>
@yield('content')
</main>

</body>
</html>"
                );
            }

            /*
            | Layout Service Provider
            */

            if (!file_exists("$layoutPath/Providers/ModuleServiceProvider.php")) {

                file_put_contents(
"$layoutPath/Providers/ModuleServiceProvider.php",
"<?php

namespace Modules\\$layoutModule\\Providers;

use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    public function boot()
    {
        \$modulePath = dirname(__DIR__);

        if (is_dir(\$modulePath.'/views')) {
            \$this->loadViewsFrom(\$modulePath.'/views','".strtolower($layoutModule)."');
        }
    }
}
");
            }

            /*
            | Register Provider Automatically
            */

            $providersFile = base_path('bootstrap/providers.php');

            if (file_exists($providersFile)) {

                $providerClass = "Modules\\$layoutModule\\Providers\\ModuleServiceProvider::class";

                $content = file_get_contents($providersFile);

                if (!str_contains($content,$providerClass)) {

                    $content = str_replace(
                        "];",
                        "    $providerClass,\n];",
                        $content
                    );

                    file_put_contents($providersFile,$content);
                }
            }
        }

        /*
        |--------------------------------
        | View
        |--------------------------------
        */

        if ($layout) {

            $viewContent = "@extends('".strtolower($layout)."::layouts.app')

@section('title','$module')

@section('content')

<h1>$module module works 🎉</h1>

@endsection";

        } else {

            $viewContent = "<h1>$module module works 🎉</h1>";
        }

        file_put_contents(
            "$modulePath/views/".strtolower($name).".blade.php",
            $viewContent
        );

        $this->info("Module $module created successfully!");
    }
}
