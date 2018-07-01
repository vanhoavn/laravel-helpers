<?php

namespace Vhnvn\LaravelHelper\Console\ModuleMakeCommands;

class ModuleMailMakeCommand extends \Illuminate\Foundation\Console\MailMakeCommand
{
    use ModuleContextOverride;
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'mmake:mail
    {module           : The module to create the event.}
    {name             : The event name.}
    {--f|force        : Create the class even if the mailable already exists.}
    {--m|markdown     : Create a new Markdown template for the mailable.}
  ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new module\'s email class';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getLocalStub()
    {
        return $this->option('markdown')
            ? __DIR__ . '/stubs/markdown-mail.stub'
            : __DIR__ . '/stubs/mail.stub';
    }

    /**
     * Write the Markdown template for the mailable.
     *
     * @return void
     * @throws \Exception
     */
    protected function writeMarkdownTemplate()
    {
        $path = $this->getPath('resources/views/' . str_replace('.', '/', $this->option('markdown')), '.blade.php');

        if (!$this->files->isDirectory(dirname($path))) {
            $this->files->makeDirectory(dirname($path), 0755, true);
        }

        $this->files->put($path, file_get_contents(__DIR__ . '/stubs/markdown.stub'));
    }
}
