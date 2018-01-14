<?php

namespace Vhnvn\LaravelHelper\Console\ModuleMakeCommands;

class ModuleProviderMakeCommand extends \Illuminate\Foundation\Console\ProviderMakeCommand
{
  use ModuleContextOverride;
  /**
   * The console command name.
   *
   * @var string
   */
  protected $name = 'mmake:provider';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Create a new module\'s provider class';

  /**
   * Get the default namespace for the class.
   *
   * @param  string  $rootNamespace
   * @return string
   */
  protected function getDefaultNamespace($rootNamespace)
  {
      return $rootNamespace;
  }
}
