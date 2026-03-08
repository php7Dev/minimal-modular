<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ModuleDisable extends Command
{
    protected $signature = 'module:disable {module}';
    protected $description = 'Disable a module';

    public function handle()
    {
        $module = $this->argument('module');

        $json = base_path("Modules/$module/module.json");

        if (!file_exists($json)) {
            $this->error("Module not found");
            return;
        }

        $config = json_decode(file_get_contents($json), true);

        $config['enabled'] = false;

        file_put_contents($json, json_encode($config, JSON_PRETTY_PRINT));

        $this->info("$module disabled");
    }
}
