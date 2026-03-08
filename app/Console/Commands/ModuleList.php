<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ModuleList extends Command
{
    protected $signature = 'module:list';
    protected $description = 'List all modules';

    public function handle()
    {
        $modulesPath = base_path('Modules');

        if (!is_dir($modulesPath)) {
            $this->error('Modules folder not found');
            return;
        }

        $modules = scandir($modulesPath);

        $this->info("Installed Modules:");

        foreach ($modules as $module) {

            if ($module === '.' || $module === '..') {
                continue;
            }

            $json = $modulesPath.'/'.$module.'/module.json';

            if (!file_exists($json)) {
                continue;
            }

            $config = json_decode(file_get_contents($json), true);

            $status = $config['enabled'] ? 'ENABLED' : 'DISABLED';

            $this->line("$module : $status");
        }
    }
}
