<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ModuleMakeAll extends Command
{
    protected $signature = 'module:make-all {module} {name}';
    protected $description = 'Create a full module with controller, model, view, middleware';

    public function handle()
    {
        $module = ucfirst($this->argument('module'));
        $name = ucfirst($this->argument('name'));

        $modulePath = base_path("Modules/$module");

        $folders = [
            "Controllers",
            "Models",
            "Middleware",
            "Providers",
            "routes",
            "views",
            "config",
            "migrations"
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
        | Route
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
        | View
        |--------------------------------
        */

        file_put_contents(
"$modulePath/views/".strtolower($name).".blade.php",
"<h1>$module module works 🎉</h1>"
        );

        $this->info("Module $module created successfully!");



        /*
        |--------------------------------
        | Service Class
        |--------------------------------
        */

            $servicePath = "$modulePath/Services";
            if (!is_dir($servicePath)) mkdir($servicePath, 0755, true);

            file_put_contents("$servicePath/{$name}Service.php", "<?php

            namespace Modules\\$module\\Services;

            use Modules\\$module\\Models\\$name;

            class {$name}Service
            {
                protected \$$name;

                public function __construct($name \$$name)
                {
                    \$this->$name = \$$name;
                }

                // Example method
                public function all()
                {
                    return \$this->{$name}::all();
                }
            }
            ");
    }
}
