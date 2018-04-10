<?php

namespace Vhnvn\LaravelHelper\Console\ModuleMakeCommands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;

class ModuleControllerMakeCommand extends \Illuminate\Routing\Console\ControllerMakeCommand
{
    use ModuleContextOverride;

    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'mmake:controller
    {module           : The module to create the controller.}
    {name             : The controller name.}
  ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new module\'s controller class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Controller';

    /**
     * Get the default namespace for the class.
     *
     * @param  string $rootNamespace
     *
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\\Http\\Controllers';
    }
}
