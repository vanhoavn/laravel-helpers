<?php

namespace Vhnvn\LaravelHelper\Console\ModuleMakeCommands;

use Illuminate\Console\GeneratorCommand;
use Vhnvn\LaravelHelper\Console\ModuleMakeCommands\ModuleContextOverride;

class ModuleJobMakeCommand extends \Illuminate\Foundation\Console\JobMakeCommand
{
    use ModuleContextOverride;
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'mmake:job
    {module : The module to create the facade.}
    {name   : The job name.}
  ';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new module\'s job class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Job';

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\\Jobs';
    }
}
