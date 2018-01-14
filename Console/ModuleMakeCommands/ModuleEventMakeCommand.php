<?php

namespace Vhnvn\LaravelHelper\Console\ModuleMakeCommands;

class ModuleEventMakeCommand extends \Illuminate\Foundation\Console\EventMakeCommand
{
  use ModuleContextOverride;
  /**
	 * The console command signature.
   *
   * @var string
   */
  protected $signature = 'mmake:event
    {module           : The module to create the event.}
    {name             : The event name.}
  ';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Create a new module\'s event class';
}
