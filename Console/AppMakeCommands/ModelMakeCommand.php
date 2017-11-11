<?php

namespace Vhnvn\LaravelHelper\Console\AppMakeCommands;


use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

class ModelMakeCommand extends GeneratorCommand
{
	/**
	* The console command name.
	* @var string
	*/
	protected $name = 'amake:model';
	
	/**
	* The console command description.
	* @var string
	*/
	protected $description = 'Create a new Eloquent model class (overrided)';

  /**
	* The type of class being generated.
  * @var string
  */
  protected $type = 'Model';
	
	/**
	* Execute the console command.
  * @return void
  */
  public function handle()
  {
		if (parent::handle() === false && ! $this->option('force')) {
			return;
		}
		
		if ($this->option('migration')) {
			$table = Str::plural(Str::snake(class_basename($this->argument('name'))));
			
			$this->call('make:migration', ['name' => "create_{$table}_table", '--create' => $table]);
		}
	}
	
	/**
	* Get the stub file for the generator.
  * @return string
  */
  protected function getStub()
  {
		return __DIR__ . '/stubs/model.stub';
	}
	
	/**
	* Get the default namespace for the class.
  * @param  string $rootNamespace
  * @return string
  */
  protected function getDefaultNamespace($rootNamespace)
  {
		return $rootNamespace;
	}
	
	/**
	* Get the console command options.
  * @return array
  */
  protected function getOptions()
  {
		return [
        ['migration', 'm', InputOption::VALUE_NONE, 'Create a new migration file for the model.'],
    ];
	}
	
	/**
	* Get the desired class name from the input.
  * @return string
  */
  protected function getNameInput()
  {
		return trim($this->argument('name'));
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

    $modelBase = config('modules.base_model');
    $modelBaseClass = str_replace($this->getNamespace($modelBase).'\\', '', $modelBase);

    $stub = str_replace(
      ['DummyBaseModelFull', 'DummyBaseModelClass'],
      [$modelBase, $modelBaseClass],
      $stub
    );

    return $this;
  }
}
