<?php

namespace Vhnvn\LaravelHelper\Console\ModuleMakeCommands;

class ModuleTestMakeCommand extends \Illuminate\Foundation\Console\TestMakeCommand
{
  use ModuleContextOverride;
  /**
	 * The console command signature.
   *
   * @var string
   */
  protected $signature = 'mmake:listener
    {module           : The module to create the listener.}
    {name             : The name of the class.}
    {--unit : Create a unit test}
  ';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Create a new module\'s listener class';
}
