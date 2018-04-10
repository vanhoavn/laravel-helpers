<?php

namespace Vhnvn\LaravelHelper\Console\ModuleMakeCommands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;

class ModuleRequestMakeCommand extends \Illuminate\Foundation\Console\RequestMakeCommand
{
    use ModuleContextOverride;

    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'mmake:request
    {module           : The module to create the request.}
    {name             : The request name.}
  ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new module\'s request class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Request';

    /**
     * Get the default namespace for the class.
     *
     * @param  string $rootNamespace
     *
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\\Http\\Requests';
    }
}
