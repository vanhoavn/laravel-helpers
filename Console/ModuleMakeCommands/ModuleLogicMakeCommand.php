<?php

namespace Vhnvn\LaravelHelper\Console\ModuleMakeCommands;

use Illuminate\Console\GeneratorCommand;

class ModuleLogicMakeCommand extends GeneratorCommand {
  use ModuleContextOverride;
  /**
   * The console command name.
   *
   * @var string
   */
  protected $name = 'mmake:logic';

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
  protected function getStub()
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
      return $rootNamespace.'\Logic';
  }

  /**
   * Parse the class name and format according to the root namespace.
   *
   * @param  string  $name
   * @return string
   */
  protected function qualifyClass($name){
    return parent::qualifyClass("{$name}Logic");
  }
}