<?php

namespace Vhnvn\LaravelHelper\Console\ModuleMakeCommands;

class ModuleEventMakeCommand extends \Illuminate\Foundation\Console\EventMakeCommand
{
  use ModuleContextOverride;
  /**
   * The console command name.
   *
   * @var string
   */
  protected $name = 'mmake:event';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Create a new module\'s event class';
}
