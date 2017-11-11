<?php

namespace Vhnvn\LaravelHelper;

use Illuminate\Support\ServiceProvider;


class ModulesServiceProvider extends ServiceProvider {
  /**
   * Bootstrap the application events.
   *
   * @return void
   */
  public function boot()
  {
    $configPath = __DIR__ . '/config/modules.php';
    if (function_exists('config_path')) {
      $publishPath = config_path('modules.php');
    } else {
      $publishPath = base_path('config/modules.php');
    }
    $this->publishes([$configPath => $publishPath], 'config');
  }

  /**
   * Register the service provider.
   *
   * @return void
   */
  public function register()
  {
    $configPath = __DIR__ . '/config/modules.php';
    $this->mergeConfigFrom($configPath, 'modules');

    $this->commands([
      \Vhnvn\LaravelHelper\Console\ModuleMakeCommands\ModuleEventMakeCommand::class,
      \Vhnvn\LaravelHelper\Console\ModuleMakeCommands\ModuleListenerMakeCommand::class,
      \Vhnvn\LaravelHelper\Console\ModuleMakeCommands\ModuleModelMakeCommand::class,
      \Vhnvn\LaravelHelper\Console\ModuleMakeCommands\ModuleProviderMakeCommand::class,

      \Vhnvn\LaravelHelper\Console\AppMakeCommands\ModelMakeCommand::class,

      \Vhnvn\LaravelHelper\Console\DataModelCommands\DataModelCodeGenerator::class,
    ]);
  }
}