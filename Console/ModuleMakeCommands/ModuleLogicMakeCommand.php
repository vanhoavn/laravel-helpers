<?php

namespace Vhnvn\LaravelHelper\Console\ModuleMakeCommands;

use Illuminate\Console\GeneratorCommand;

class ModuleLogicMakeCommand extends GeneratorCommand {
  use ModuleContextOverride;
  /**
 	 * The console command signature.
   *
   * @var string
   */
  protected $signature = 'mmake:logic
    {module : The module to create the logic.}
    {name   : The logic name.}
  ';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Create a new module\'s logic class';

  /**
   * The type of class being generated.
   *
   * @var string
   */
  protected $type = 'Logic';

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
      return __DIR__.'/stubs/logic.stub';
  }

  /**
   * Get the default namespace for the class.
   *
   * @param  string  $rootNamespace
   * @return string
   */
  protected function getDefaultNamespace($rootNamespace)
  {
      return $rootNamespace . '\\Logic';
  }
}