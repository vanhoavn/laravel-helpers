<?php

namespace Vhnvn\LaravelHelper\Console\ModuleMakeCommands;

use \Illuminate\Console\Command;
use Illuminate\Support\Str;

class ModuleNewCommand extends Command {
  /**
	 * The console command signature.
   *
   * @var string
   */
  protected $signature = 'mmake
    {module : The module to create/update.}
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
    'Exception',
    'Facades',
    'Logic',
    'Model',
    'Repository',
  ];

  /**
   * Execute the console command.
   *
   * @return bool|null
   */
  public function handle()
  {
    $module = str_replace('\\', '/', $this->input->getArgument('module'));
    $base_path = $this->laravel->basePath() . '/' . config('modules.path') . '/' . str_replace("\\", "/", $module);

    $name = str_replace("/", "", $module);

    if (Str::endsWith($name, 'Module')) {
      $name = substr($name, 0, -6);
    }

    $this->info("Creating module {$name} at {$module}");

    $this->info("  .. create {$base_path}");
    $this->mkdir($base_path);

    foreach(self::STRUCTURES as $structure) {
      $targetFunction = "create{$structure}Structure";
      $this->info("  .. create structure {$structure}");
      if (method_exists($this, $targetFunction)) {
        $this->$targetFunction($base_path, $module, $name);
      } else {
        $this->createGenericStructure($base_path, $structure, $module,$name);
      }
    }

    $this->info("  .. create module provider");
    $this->call('mmake:provider', [
      'module' => $module,
      'name' => "{$name}ModuleProvider",
      '--registerLogic' => "{$name}Logic",
    ]);
  }

  protected function createGenericStructure($base_path, $structure, $module, $name) {
    $path = "$base_path/$structure";
    $this->mkdir($path);
    $this->gitkeep($path);
  }

  protected function createLogicStructure($base_path, $module, $name) {
    $this->createGenericStructure($base_path, 'Logic', $module, $name);
    $this->call('mmake:logic', [
      'module' => $module,
      'name' => "{$name}Logic",
    ]);
    $this->call('mmake:facade', [
      'module' => $module,
      'name' => "{$name}Module",
      'target' => 'Logic\\' . $name . 'Logic',
    ]);
  }

  protected function createRepositoryStructure($base_path, $module, $name) {
    $this->createGenericStructure($base_path, 'Repository', $module, $name);
    $this->createGenericStructure($base_path, 'Repository/Contracts', $module, $name);
    $this->createGenericStructure($base_path, 'Repository/Default', $module, $name);
  }

  protected function mkdir($dir) {
    @\mkdir($dir, 0777, true);
    if(!file_exists($dir) || !is_dir($dir)) {
      $this->error("Failed to create $dir");
      throw new \Exception("Failed to create $dir");
    }
  }

  protected function gitkeep($dir) {
    if(!\file_exists("$dir/.gitkeep")) {
      file_put_contents("$dir/.gitkeep", "");
    }
    if(!\file_exists("$dir/.gitkeep")) {
      $this->error("Failed to git keep $dir");
      throw new \Exception("Failed to git keep $dir");
    }
  }

  /**
   * Get the root namespace for the class.
   *
   * @return string
   */
  protected function rootNamespace()
  {
    return config('modules.namespace', $this->laravel->getNamespace());
  }
}