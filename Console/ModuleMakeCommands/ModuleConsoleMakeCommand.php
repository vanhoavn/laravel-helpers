<?php

namespace Vhnvn\LaravelHelper\Console\ModuleMakeCommands;

use Illuminate\Console\GeneratorCommand;

class ModuleConsoleMakeCommand extends GeneratorCommand {
  use ModuleContextOverride;
  /**
 	 * The console command signature.
   *
   * @var string
   */
  protected $signature = 'mmake:command
    {module : The module to create the logic.}
    {name   : The command name.}
  ';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Create a new module\'s command class';

  /**
   * The type of class being generated.
   *
   * @var string
   */
  protected $type = 'Console command';

  /**
   * Determine if the class already exists.
   *
   * @param  string  $rawName
   * @return bool
   */
  protected function alreadyExists($rawName)
  {
      return class_exists($rawName);
  }

  /**
   * Get the stub file for the generator.
   *
   * @return string
   */
  protected function getLocalStub()
  {
      return __DIR__.'/stubs/console.stub';
  }

  /**
   * Get the default namespace for the class.
   *
   * @param  string  $rootNamespace
   * @return string
   */
  protected function getDefaultNamespace($rootNamespace)
  {
      return $rootNamespace . '\\Commands';
  }
}