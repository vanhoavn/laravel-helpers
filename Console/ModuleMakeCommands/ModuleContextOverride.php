<?php

namespace Vhnvn\LaravelHelper\Console\ModuleMakeCommands;

use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

trait ModuleContextOverride
{
    /**
     * Get the root namespace for the class.
     *
     * @return string
     */
    protected function rootNamespace()
    {
        if ($this->input->hasArgument('module')) {
            return $this->rootModuleNamespace();
        } else {
            return config('modules.namespace', $this->laravel->getNamespace());
        }
    }

    /**
     * Get the module's root namespace for the class.
     *
     * @return string
     */
    protected function rootModuleNamespace()
    {
        $module_ns = str_replace('/', '\\', $this->input->getArgument('module'));

        return config("{$this->configKeyRoot()}.namespace", $this->laravel->getNamespace()) . '\\' . $module_ns;
    }

    private function configKeyRoot()
    {
        $config_key = "modules";
        if ($this->input->hasOption('ns')) {
            $ns = $this->input->getOption('ns') ?? 'default';
            if (array_key_exists($ns, config('modules.sub_namespace'))) {
                $config_key = "modules.sub_namespace.$ns";
            } else {
                throw new \Exception("Cant find sub_namespace $ns");
            }
        }
        return $config_key;
    }

    /**
     * Get the destination class path.
     *
     * @param  string $name
     * @param string  $ext
     *
     * @return string
     * @throws \Exception
     */
    protected function getPath($name, $ext = ".php")
    {
        $name = Str::replaceFirst($this->rootNamespace(), '', $name);

        if ($this->input->hasArgument('module')) {
            $module_path = str_replace('\\', '/', $this->input->getArgument('module'));
            return $this->laravel->basePath() . '/' . config("{$this->configKeyRoot()}.path") . '/' . $module_path . '/' . str_replace('\\', '/', $name) . $ext;
        } else {
            return $this->laravel->basePath() . '/' . config("{$this->configKeyRoot()}.path") . '/' . str_replace('\\', '/', $name) . $ext;
        }
    }

    protected function configureUsingFluentDefinition()
    {
        parent::configureUsingFluentDefinition();
        $this->getDefinition()->addOption(new InputOption("ns", "s", InputOption::VALUE_OPTIONAL, "module root name"));
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        $stub     = method_exists($this, 'getLocalStub') ? $this->getLocalStub() : parent::getStub();
        $filename = basename($stub);
        $folder   = basename(dirname($stub));

        if ($folder === 'stubs') {
            if ($stubs = config("modules.stubs")) {
                if (array_key_exists($filename, $stubs)) {
                    return $stubs[$filename];
                }
            }
        }

        return $stub;
    }

    private function wrapNS($option)
    {
        if ($this->input->hasOption('ns')) {
            $ns = $this->input->getOption('ns') ?? 'default';
            if (array_key_exists($ns, config('modules.sub_namespace'))) {
                return [
                        "--ns" => $ns,
                    ] + $option;
            } else {
                throw new \Exception("Cant find sub_namespace $ns");
            }
        }
        return $option;
    }
}