<?php

namespace Vhnvn\LaravelHelper\Console\ModuleMakeCommands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;

class ModuleNotificationMakeCommand extends \Illuminate\Foundation\Console\NotificationMakeCommand
{
    use ModuleContextOverride;

    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'mmake:notification
    {module            : The module to create the notification.}
    {name              : The notification name.}
    {--m|markdown      : Create a new Markdown template for the notification.}
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
     * Write the Markdown template for the mailable.
     *
     * @return void
     * @throws \Exception
     */
    protected function writeMarkdownTemplate()
    {
        $path = $this->getPath('resources/views/' . str_replace('.', '/', $this->option('markdown'))) . '.blade.php';

        if (!$this->files->isDirectory(dirname($path))) {
            $this->files->makeDirectory(dirname($path), 0755, true);
        }

        $this->files->put($path, file_get_contents(__DIR__ . '/stubs/markdown.stub'));

    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string $rootNamespace
     *
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\\Notifications';
    }
}
