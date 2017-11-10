<?php

namespace Vhnvn\LaravelHelper\Console\ModuleMakeCommands;

class ModuleListenerMakeCommand extends \Illuminate\Foundation\Console\ListenerMakeCommand
{
  use ModuleContextOverride;
  /**
   * The console command name.
   *
   * @var string
   */
  protected $name = 'mmake:listener';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Create a new module\'s listener class';
}
