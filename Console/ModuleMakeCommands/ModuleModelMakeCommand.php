<?php

namespace Vhnvn\LaravelHelper\Console\ModuleMakeCommands;

use \Vhnvn\LaravelHelper\Console\AppMakeCommands\ModelMakeCommand;

class ModuleModelMakeCommand extends ModelMakeCommand {
  use ModuleContextOverride;
  /**
	 * The console command signature.
   *
   * @var string
   */
  protected $signature = 'mmake:model
    {module           : The module to create the model.}
    {name             : The model name.}
    {--m|migration  : Create a new migration file for the model.}
  ';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Create a new module\'s Eloquent model class (overrided)';

	/**
	* Execute the console command.
  * @return void
  */
  public function handle()
  {
		if (GeneratorCommand::handle() === false && ! $this->option('force')) {
			return;
		}

		if ($this->option('migration')) {
			$table = Str::plural(Str::snake(class_basename($this->argument('name'))));

			$this->call('mmake:migration', [
        'module' => $this->argument('module'),
        'name' => "create_{$table}_table",
        '--create' => $table
      ]);
		}
  }
}