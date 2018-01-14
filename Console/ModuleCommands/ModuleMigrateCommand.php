<?php

namespace Vhnvn\LaravelHelper\Console\ModuleCommands;

class ModuleMigrateCommand extends \Illuminate\Database\Console\Migrations\MigrateMakeCommand
{

	/**
	* The console command signature.
  *
  * @var string
  */
  protected $signature = 'mmigrate
    {--module= : The module to migrate.}
    {--database= : The database connection to use.}
    {--force : Force the operation to run when in production.}
    {--pretend : Dump the SQL queries that would be run.}
    {--seed : Indicates if the seed task should be re-run.}
    {--step : Force the migrations to be run so they can be rolled back individually.}';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Run the database migrations including module migrations';

  /**
   * Execute the console command.
   *
   * @return void
   */
  public function handle() {
    foreach($this->findModuleMigrationPaths() as $path) {
      $this->migrator->path($path);
    }
    return parent::handle();
  }

  /**
   * Find all migrations path in all modules if no 'module' option provided, else return the paths for that module.
   *
   * @return Generator
   */
  protected function findModuleMigrationPaths() {
    if ($this->input->hasOption('module')) {
      yield from findSingleModuleMigrationPaths($this->input->getOption('module'));
    } else {
      $base_module_path = rtrim($this->getPath(''), '/');
      yield from findModuleMigrationPathsFromPath($base_module_path, 2);
    }
  }

  /**
   * Find all migration paths from base directory
   *
   * @param string $path
   * @param string $depth
   * @return void
   */
  protected function findModuleMigrationPathsFromPath($path, $depth) {
    foreach(scandir($path) as $module) {
      if(!in_array($module, ['.', '..']) && is_dir($module)) {
        yield from $this->findSingleModuleMigrationPaths($path . '/' . $module);
        if($depth > 0) {
          $this->findModuleMigrationPathsFromPath($path . '/' . $module, $depth - 1);
        }
      }
    }
  }

  /**
   * Find all migrations path for a specified module
   *
   * @param string $module
   * @return Generator
   */
  protected function findSingleModuleMigrationPaths($module) {
    $target_path = $this->getPath($module, 'database/migrations');
    if (file_exists($target_path)) {
      yield $target_path;
    }
  }

  /**
   * Get the destination path.
   *
   * @param  string $module
   * @return string $name
   */
  protected function getPath($module, $name)
  {
    $module_ns = str_replace('\\', '/', $module);

    return $this->laravel->basePath() . '/' . config('modules.path') . '/' . $module_ns . '/' . str_replace('\\', '/', $name);
  }
}
