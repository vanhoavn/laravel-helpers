<?php

namespace Vhnvn\LaravelHelper\Console\ModuleMakeCommands;

class ModuleMigrateMakeCommand extends \Illuminate\Database\Console\Migrations\MigrateMakeCommand
{
    use ModuleContextOverride;

    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'mmake:migration
        {module : The name of the module.}
        {name : The name of the migration.}
        {--create= : The table to be created.}
        {--table= : The table to migrate.}
        {--path= : The location where the migration file should be created.}';


    /**
     * Get migration path (either specified by '--path' option or default location).
     *
     * @return string
     */
    protected function getMigrationPath()
    {
        if (!is_null($targetPath = $this->input->getOption('path'))) {
            return $this->getPath($targetPath);
        }

        return $this->getPath('databases/migrations');
    }
}
