<?php

namespace Vhnvn\LaravelHelper\Console\ModuleMakeCommands;

class ModuleTestMakeCommand extends \Illuminate\Foundation\Console\TestMakeCommand
{
    use ModuleContextOverride;
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'mmake:test
    {module           : The module to create the listener.}
    {name             : The name of the class.}
    {--unit : Create a unit test}
  ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new module\'s test class';

    /**
     * Get the default namespace for the class.
     *
     * @param string $rootNamespace
     *
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\\Test';
    }
}
