<?php

namespace Vhnvn\LaravelHelper\Console\ModuleMakeCommands;

class ModuleListenerMakeCommand extends \Illuminate\Foundation\Console\ListenerMakeCommand
{
  use ModuleContextOverride;
  /**
	 * The console command signature.
   *
   * @var string
   */
  protected $signature = 'mmake:listener
    {module           : The module to create the listener.}
    {name             : The listener name.}
    {--e|event=       : The event class being listened for.}
    {--queued         : Indicates the event listener should be queued.}
  ';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Create a new module\'s listener class';
}
