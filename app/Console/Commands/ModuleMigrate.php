<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ModuleMigrate extends Command
{
    protected $signature = 'module:migrate {module}';
    protected $description = 'Run migrations for a module';

    public function handle()
    {
        $module = $this->argument('module');

        $path = "Modules/$module/migrations";

        if (!is_dir(base_path($path))) {
            $this->error("No migrations found");
            return;
        }

        Artisan::call('migrate', [
            '--path' => $path
        ]);

        $this->info("Migrations executed for $module");
    }
}
