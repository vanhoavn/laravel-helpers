<?php

namespace Vhnvn\LaravelHelper\Console\ModuleMakeCommands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;

class ModuleRepositoryMakeCommand extends Command
{
    use ModuleContextOverride;
    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * Create a new controller creator command instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @return void
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'mmake:repository
    {module           : The module to create the repository.}
    {name             : The repository name.}
    {--r|register     : Register the repository within service provider.}
  ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new module\'s repository class';


    /**
     * Execute the console command.
     *
     * @return bool|null
     */
    public function handle()
    {
        $name = $this->argument('name');
        $name = str_replace('\\', '/', $name);

        $contract = $this->generateContract($name);
        $repository = $this->generateRepository($name, $contract);

        $this->info('Created contract and repository.');

        if ($this->input->getOption('register')) {
            $this->registerWithinServiceProvider($contract, $repository);
            $this->info('Registered binding.');
        }
    }

    protected function generateContract($name) {
        $contract_base = substr($this->getPath('Repository/Contracts'), 0, -4);
        $contract_ns = $this->rootModuleNamespace() . '\\Repository\\Contracts';

        if(($pos = strrpos($name, '/')) !== false) {
            $ns = substr($name, 0, $pos);
            $name = substr($name, $pos + 1);
            $contract_ns .= '\\' . $ns;
            $contract_base .= '/' . str_replace('\\','/',$ns);
        }

        $interface_name = "{$name}Interface";

        $this->generateStubFile(
            $contract_base . "/" . $interface_name . ".php",
            __DIR__ . '/stubs/repository_interface.stub',
            [
                'DummyNamespace' => $contract_ns,
                'DummyInterface' => $interface_name,
            ]
        );

        return [
            $contract_ns,
            $interface_name
        ];
    }

    protected function generateRepository($name, $interface) {
        $repository_base = substr($this->getPath('Repository'), 0, -4);
        $repository_ns = $this->rootModuleNamespace() . '\\Repository';

        if($this->files->exists($repository_base . '/Default') && $this->files->isDirectory($repository_base . '/Default')) {
            $repository_base .= '/DefaultImplement';
            $repository_ns .= '\\DefaultImplement';
        }

        if(($pos = strrpos($name, '/')) !== false) {
            $ns = substr($name, 0, $pos);
            $name = substr($name, $pos + 1);
            $repository_ns .= '\\' . $ns;
            $repository_base .= '/' . str_replace('\\','/',$ns);
        }

        $repository_name = "{$name}Repository";

        $this->generateStubFile(
            $repository_base . "/" . $repository_name . ".php",
            __DIR__ . '/stubs/repository.stub',
            [
                'DummyNamespace' => $repository_ns,
                'DummyClass' => $repository_name,
                'DummyInterface' => implode('\\', $interface),
                'DummyBaseInterface' => $interface[1],
            ]
        );

        return [
            $repository_ns,
            $repository_name
        ];
    }

    private function generateStubFile($target, $stub_file, $vars) {
        if ($this->files->exists($target)) {
            throw new \Exception("$target already exists");
        }
        $content = $this->files->get($stub_file);
        $content = str_replace(array_keys($vars), array_values($vars), $content);
        $this->files->put($target, $content);
    }

    protected function registerWithinServiceProvider($contract, $repository) {
        $service_provider_file = $this->getPath($this->input->getArgument('module').'ModuleProvider');

        if (!$this->files->exists($service_provider_file)) {
            $this->error("$service_provider_file does not exists");
            return;
        }

        $content = $this->files->get($service_provider_file);

        if(strpos($content, '// END - REPOSITORIES REGISTRY')===false) {
            $this->error("\"// END - REPOSITORIES REGISTRY\" marker is missing within $service_provider_file.");
            return;
        }

        $content = preg_replace_callback('@^(\s+)// END - REPOSITORIES REGISTRY@m', function ($m) use ($contract, $repository) {
            return $m[1] . '$this->app->bind(\\'.implode('\\',$contract).'::class, \\'.implode('\\',$repository).'::class);' . "\n" . $m[0];
        }, $content);

        $this->files->put($service_provider_file, $content);
    }
}
