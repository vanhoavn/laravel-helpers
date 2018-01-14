<?php

namespace Vhnvn\LaravelHelper\Console\ModuleMakeCommands;

use Illuminate\Support\Str;

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

    return config('modules.namespace', $this->laravel->getNamespace()) . '\\' . $module_ns;
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
      return $this->laravel->basePath() . '/' . config('modules.path') . '/' . $module_path . '/' . str_replace('\\', '/', $name) . '.php';
    } else {
      return $this->laravel->basePath() . '/' . config('modules.path') . '/' . str_replace('\\', '/', $name) . '.php';
    }
  }
}