<?php

namespace Vhnvn\LaravelHelper\Console\ModuleMakeCommands;

use Illuminate\Support\Str;

class ModuleProviderMakeCommand extends \Illuminate\Foundation\Console\ProviderMakeCommand
{
  use ModuleContextOverride;
  /**
 	 * The console command signature.
   *
   * @var string
   */
  protected $signature = 'mmake:provider
    {module           : The module to create the provider.}
    {name             : The provider name.}
    {--registerLogic= : Logic class to register singeton.}
  ';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Create a new module\'s provider class';

  /**
   * Get the stub file for the generator.
   *
   * @return string
   */
  protected function getStub()
  {
      return __DIR__.'/stubs/provider.stub';
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

    $code = [];

    if ($this->input->hasOption('registerLogic')) {
      $logic = $this->input->getOption('registerLogic');
      $logic = str_replace("/", "", $logic);
      $logic = str_replace("\\", "", $logic);

      $code[] = '$this->app->singleton('.$this->rootModuleNamespace().'\\Logic\\'.$logic.'::class);';
    }

    return str_replace(
      ['DummyCode', 'dummyname' ],
      [implode("\n        ", $code), Str::snake($name), ],
      $ret
    );
  }

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
