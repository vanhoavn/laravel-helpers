<?php

namespace Vhnvn\LaravelHelper\Console\ModuleMakeCommands;

use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

trait ModuleContextOverride {
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

  private function configKeyRoot() {
    $config_key = "modules";
    if ($this->input->hasOption('ns')) {
      $ns = $this->input->getOption('ns');
      if(array_key_exists($ns, config('modules.sub_namespace'))) {
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
   * @param  string  $name
   * @return string
   */
  protected function getPath($name)
  {
    $name = Str::replaceFirst($this->rootNamespace(), '', $name);

    if ($this->input->hasArgument('module')) {
      $module_path = str_replace('\\', '/', $this->input->getArgument('module'));
      return $this->laravel->basePath() . '/' . config('{$this->configKeyRoot()}.path') . '/' . $module_path . '/' . str_replace('\\', '/', $name) . '.php';
    } else {
      return $this->laravel->basePath() . '/' . config('{$this->configKeyRoot()}.path') . '/' . str_replace('\\', '/', $name) . '.php';
    }
  }

  protected function configureUsingFluentDefinition() {
    parent::configureUsingFluentDefinition();
    $this->getDefinition()->addOption(new InputOption("ns", "ns", InputOption::VALUE_NONE, "module root name"));
  }
}