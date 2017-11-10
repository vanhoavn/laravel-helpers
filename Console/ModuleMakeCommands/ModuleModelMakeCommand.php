<?php

namespace Vhnvn\LaravelHelper\Console\ModuleMakeCommands;

use \Vhnvn\LaravelHelper\Console\AppMakeCommands\ModelMakeCommand;

class ModuleModelMakeCommand extends ModelMakeCommand {
  use ModuleContextOverride;
  /**
   * The console command name.
   *
   * @var string
   */
  protected $name = 'mmake:model';
  
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new module\'s Eloquent model class (overrided)';
}