<?php

namespace Vhnvn\LaravelHelper\Console\ModuleMakeCommands;

use Illuminate\Console\GeneratorCommand;

class ModuleFacadeMakeCommand extends GeneratorCommand {
  use ModuleContextOverride;

  /**
 	 * The console command signature.
   *
   * @var string
   */
  protected $signature = 'mmake:facade
    {module : The module to create the facade.}
    {name   : The facade name.}
    {target : The target class.}
  ';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Create a new module\'s facade class';

  /**
   * The type of class being generated.
   *
   * @var string
   */
  protected $type = 'Facade';

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
      return __DIR__.'/stubs/facade.stub';
  }

  /**
   * Get the default namespace for the class.
   *
   * @param  string  $rootNamespace
   * @return string
   */
  protected function getDefaultNamespace($rootNamespace)
  {
      return $rootNamespace.'\Facades';
  }

  /**
   * Replace the namespace for the given stub.
   *
   * @param  string  $stub
   * @param  string  $name
   * @return $this
   */
  protected function replaceNamespace(&$stub, $name)
  {
    parent::replaceNamespace($stub, $name);

    $stub = str_replace(
      ['DummyModuleNamespace'],
      [$this->rootModuleNamespace()],
      $stub
    );

    return $this;
  }

  /**
   * Replace the class name for the given stub.
   *
   * @param  string  $stub
   * @param  string  $name
   * @return string
   */
  protected function replaceClass($stub, $name)
  {
    $ret = parent::replaceClass($stub, $name);

    $target = $this->input->getArgument('target');
    $target_full = str_replace('/', '\\', $target);
    $target_base = trim(strrchr($target_full, '/') ?? $target_full, '/');

    return str_replace(
      ['DummyTargetFull', 'DummyTargetBase', ],
      [$target_full, $target_base, ],
    $ret);
  }

    /**
   * Parse the class name and format according to the root namespace.
   *
   * @param  string  $name
   * @return string
   */
  protected function qualifyClass($name){
    return parent::qualifyClass($name);
  }
}