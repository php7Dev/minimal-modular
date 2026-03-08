<?php

namespace App\Support;

class ModuleLoader
{
    public static function providers(): array
    {
        $modulesPath = base_path('Modules');

        if (!is_dir($modulesPath)) {
            return [];
        }

        $providers = [];

        foreach (scandir($modulesPath) as $module) {

            if ($module === '.' || $module === '..') {
                continue;
            }

            $moduleJson = $modulesPath.'/'.$module.'/module.json';

            if (!file_exists($moduleJson)) {
                continue;
            }

            $config = json_decode(file_get_contents($moduleJson), true);

            if (!empty($config['enabled']) && !empty($config['provider'])) {
                $providers[] = $config['provider'];
            }
        }

        return $providers;
    }
}
