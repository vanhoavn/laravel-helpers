<?php

namespace Vhnvn\LaravelHelper\Console\ModuleMakeCommands;

use \Illuminate\Console\Command;
use Illuminate\Support\Str;

class ModuleNewCommand extends Command
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'mmake
    {module : The module to create/update.}
    {--ns=  : The namespace to create/update.}
  ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new module';

    /**
     * The structures that will be created
     */
    const STRUCTURES = [
        'DataModel',
        'Commands',
        'Jobs',
        'Http',
        'Exception',
        'Facades',
        'Logic',
        'Model',
        'databases',
        'resources',
        'Repository',
    ];

    private function configKeyRoot()
    {
        $config_key = "modules";
        if ($this->input->hasOption('ns')) {
            $ns = $this->input->getOption('ns', 'default');
            if (array_key_exists($ns, config('modules.sub_namespace'))) {
                $config_key = "modules.sub_namespace.$ns";
            } else {
                throw new \Exception("Cant find sub_namespace $ns");
            }
        }
        return $config_key;
    }

    /**
     * Execute the console command.
     *
     * @return bool|null
     */
    public function handle()
    {
        $module = str_replace('\\', '/', $this->input->getArgument('module'));
        $base_path = $this->laravel->basePath() . '/' . config("{$this->configKeyRoot()}.path") . '/' . str_replace("\\", "/", $module);

        $name = str_replace("/", "", $module);

        if (Str::endsWith($name, 'Module')) {
            $name = substr($name, 0, -6);
        }

        $this->info("Creating module {$name} at {$module}");

        $this->info("  .. create {$base_path}");
        $this->mkdir($base_path);

        foreach (self::STRUCTURES as $structure) {
            $targetFunction = "create{$structure}Structure";
            $this->info("  .. create structure {$structure}");
            if (method_exists($this, $targetFunction)) {
                $this->$targetFunction($base_path, $module, $name);
            } else {
                $this->createGenericStructure($base_path, $structure, $module, $name);
            }
        }

        $this->info("  .. create module provider");
        $this->call('mmake:provider', $this->wrapNS([
            'module'          => $module,
            'name'            => "{$name}ModuleProvider",
            '--registerLogic' => "{$name}Logic",
        ]));


    }

    protected function createGenericStructure($base_path, $structure, $module, $name)
    {
        $path = "$base_path/$structure";
        $this->mkdir($path);
        $this->gitkeep($path);
    }

    protected function createLogicStructure($base_path, $module, $name)
    {
        $this->createGenericStructure($base_path, 'Logic', $module, $name);
        $this->call('mmake:logic', $this->wrapNS([
            'module' => $module,
            'name'   => "{$name}Logic",
        ]));
        $this->call('mmake:facade', $this->wrapNS([
            'module' => $module,
            'name'   => "{$name}Module",
            'target' => 'Logic\\' . $name . 'Logic',
        ]));
    }

    protected function createRepositoryStructure($base_path, $module, $name)
    {
        $this->createGenericStructure($base_path, 'Repository', $module, $name);
        $this->createGenericStructure($base_path, 'Repository/Contracts', $module, $name);
        $this->createGenericStructure($base_path, 'Repository/DefaultImplement', $module, $name);
    }

    protected function createdatabasesStructure($base_path, $module, $name)
    {
        $this->createGenericStructure($base_path, 'databases', $module, $name);
        $this->createGenericStructure($base_path, 'databases/migrations', $module, $name);
    }

    protected function createHttpStructure($base_path, $module, $name)
    {
        $this->createGenericStructure($base_path, 'Http', $module, $name);
        $this->createGenericStructure($base_path, 'Http/Controllers', $module, $name);
        $this->createGenericStructure($base_path, 'Http/Requests', $module, $name);
    }

    protected function createresourcesStructure($base_path, $module, $name)
    {
        $this->createGenericStructure($base_path, 'resources', $module, $name);
        $this->createGenericStructure($base_path, 'resources/translations', $module, $name);
        $this->createGenericStructure($base_path, 'resources/views', $module, $name);
    }

    protected function mkdir($dir)
    {
        @\mkdir($dir, 0777, true);
        if (!file_exists($dir) || !is_dir($dir)) {
            $this->error("Failed to create $dir");
            throw new \Exception("Failed to create $dir");
        }
    }

    protected function gitkeep($dir)
    {
        if (!\file_exists("$dir/.gitkeep")) {
            file_put_contents("$dir/.gitkeep", "");
        }
        if (!\file_exists("$dir/.gitkeep")) {
            $this->error("Failed to git keep $dir");
            throw new \Exception("Failed to git keep $dir");
        }
    }

    private function wrapNS($option)
    {
        if ($this->input->hasOption('ns')) {
            $ns = $this->input->getOption('ns');
            if (array_key_exists($ns, config('modules.sub_namespace'))) {
                return [
                        "--ns" => $ns,
                    ] + $option;
            } else {
                throw new \Exception("Cant find sub_namespace $ns");
            }
        }
        return $option;
    }

    /**
     * Get the root namespace for the class.
     *
     * @return string
     */
    protected function rootNamespace()
    {
        return config("{$this->configKeyRoot()}.namespace", $this->laravel->getNamespace());
    }
}