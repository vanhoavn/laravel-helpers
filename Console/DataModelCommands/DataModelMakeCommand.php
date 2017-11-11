<?php

namespace Vhnvn\LaravelHelper\Console\DataModelCommands;

use Illuminate\Console\Command;

class DataModelCodeGenerator extends Command
{
	const SECTION_NAME = 'DataModelCodeGenerator Auto Generated Section';
	
	/**
	* The name and signature of the console command.
  *
  * @var string
  */
  protected $signature = 'datamodel:update {class}';
	
	
	/**
	* The console command description.
  *
  * @var string
  */
  protected $description = "Update DataModel's generated definitions";
	
	
	/**
	* Execute the console command.
  *
  * @return mixed
  */
  public function handle()
  {
		$class = $this->argument('class');
		if (!class_exists($class) && file_exists($class)) {
			$class = $this->resolveClassFromFile($class);
		}
		else if (!class_exists($class) && strpos($class, "\\") === false) {
			$class = $this->resolveClass($class);
		}
		if (!class_exists($class)) {
			$this->error("Unknown class: $class");
			exit(-1);
		}
		$this->updateClassModel($class);
		return null;
	}
	
	private function resolveClass($class)
	{
		$this->info("Resolving class $class");
		$path = app_path();
		$this->info("Application path = $path");
		$matched_file = null;
		foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path)) as $file) {
			if ($file->getBasename() === "$class.php") {
				if (!is_null($matched_file)) {
					$this->error("Multiple matches for $class: $matched_file and $file");
					exit(-1);
				}
				$matched_file = $file->getPathname();
			}
		}
		if (is_null($matched_file)) {
			$this->error("Failed to resolve $class");
			exit(-1);
		}
		
		$result = $this->resolveClassFromFile($matched_file);
		
		$this->info("Resolved class: $result");
		
		return $result;
	}
	
	function resolveClassFromFile($file)
	{
		$code = file_get_contents($file);
		
		if (!preg_match('@^namespace\s+([^;\s]+);@ism', $code, $m)) {
			$this->error("Failed to resolve namespace in $file");
			exit(-1);
		}
		
		$result = "\\" . trim($m[1], "\\");
		$result .= "\\" . str_replace(".php", "", basename($file));
		return $result;
	}
	
	private function indent($level)
	{
		return str_repeat(" ", 4 * $level);
	}
	
	private function updateClassModel($class)
	{
		$this->info("Update class $class");
		
		$class_reflection = new \ReflectionClass($class);
		$path = $class_reflection->getFileName();
		
		$this->info("Target path: $path");
		
		$required_properties = [];
		$optional_properties = [];
		
		$parent_class = $class_reflection->getParentClass();
		
		$prefix_parameters = $this->getPrefixParameters($parent_class);
		
		foreach ($class_reflection->getProperties(\ReflectionProperty::IS_PRIVATE) as $property) {
			$typing = $this->resolvePropertyTyping($property);
			if (in_array("null", explode("|", strtolower($typing)))) {
				$optional_properties [] = [
				                    'name' => $property->getName(),
				                    'type' => $typing,
				                ];
			}
			else {
				$required_properties [] = [
				                    'name' => $property->getName(),
				                    'type' => $typing,
				                ];
			}
		}
		
		usort($required_properties, function ($a, $b) {
			return $a['name'] > $b['name'];
		}
		);
		
		$code = $this->generateCode($prefix_parameters, $required_properties, $optional_properties);
		
		$this->injectGeneratedCodeIntoClass($path, $code);
	}
	
	
	/**
	* @param \ReflectionClass $parent_class
  *
  * @return array
  */
  private function getPrefixParameters($parent_class)
  {
		$prefix_parameters = [];
		
		if ($parent_class) {
			$parent_constructor = $parent_class->getConstructor();
			if ($parent_constructor) {
				$doc_block = $parent_constructor->getDocComment();
				
				$no_of_parameters = $parent_constructor->getNumberOfParameters();
				
				if (preg_match_all('#@param\s+([^\s]+)#ism', $doc_block, $parameter_matches) !== $no_of_parameters) {
					$this->error("Invalid parameters count on super class");
					exit(-1);
				}
				
				$parameter_matches = $parameter_matches[1];
				
				foreach ($parent_constructor->getParameters() as $i => $parameter) {
					$prefix_parameters [] = [
					                        'name' => $parameter->getName(),
					                        'type' => $parameter_matches[$i],
					                    ];
				}
			}
		}
		return $prefix_parameters;
	}
	
	
	/**
	* @param \ReflectionProperty $property
  */
  private function resolvePropertyTyping($property)
  {
		$doc_block = $property->getDocComment();
		if (preg_match_all('#@var\s+([^\s]+)#ism', $doc_block, $m, PREG_SET_ORDER) !== 1) {
			$this->error("Failed to resolve typing for property {$property->getName()}");
			exit(-1);
		}
		return $m[0][1];
	}
	
	// 	region Code generation
  private function generateCode($prefix_parameters, $required_properties, $optional_properties)
  {
		return (
		            "{$this->generateRequiredCode(1, $prefix_parameters, $required_properties)}" .
		            "{$this->generateOptionalCode(1, $optional_properties)}" .
		            "{$this->generateGetters(1, array_merge($required_properties, $optional_properties))}"
		        );
	}
	
	private function generateRequiredCode($indent_level, $prefix_parameters, $required_properties)
  {
		if (empty($required_properties) && empty($prefix_parameters)) {
			ob_start();
			echo "\n";
			echo "{$this->indent($indent_level)}public function __construct()\n";
			echo "{$this->indent($indent_level)}{\n";
			echo "{$this->indent($indent_level)}}\n";
			// 			echo "\n";
			// 			echo "{$this->indent($indent_level)}public static function make()\n";
			// 			echo "{$this->indent($indent_level)}{\n";
			// 			echo "{$this->indent($indent_level+1)}return new self;\n";
			// 			echo "{$this->indent($indent_level)}}\n";
			return ob_get_clean();
		}
		return (
		            "{$this->generateConstructorParametersDocBlock($indent_level, $prefix_parameters, $required_properties)}" .
		            "{$this->generateConstructor($indent_level, $prefix_parameters, $required_properties)}" .
		// 		"{$this->generateStaticMakeParametersDocBlock($indent_level, $prefix_parameters, $required_properties)}" .
		// 		"{$this->generateStaticMake($indent_level, $prefix_parameters, $required_properties)}" .
		            ""
		        );
	}
	
	private function generateConstructorParametersDocBlock($indent_level, $prefix_parameters, $required_properties)
  {
		ob_start();
		echo "\n";
		echo "{$this->indent($indent_level)}/**\n";
		foreach ($prefix_parameters as $property) {
			echo "{$this->indent($indent_level)} * @param {$property['type']} \${$property['name']}\n";
		}
		foreach ($required_properties as $property) {
			echo "{$this->indent($indent_level)} * @param {$property['type']} \${$property['name']}\n";
		}
		echo "{$this->indent($indent_level)} *\n";
		echo "{$this->indent($indent_level)} * @throws \\InvalidArgumentException\n";
		echo "{$this->indent($indent_level)} **/\n";
		return ob_get_clean();
	}
	
	private function generateConstructor($indent_level, $prefix_parameters, $required_properties)
  {
		$no_of_prefix = count($prefix_parameters);
		$no_properties = count($required_properties);
		$combined = array_merge($prefix_parameters, $required_properties);
		ob_start();
		echo "{$this->indent($indent_level)}function __construct(\n";
		for ($i = 0; $i < $no_of_prefix + $no_properties; $i++) {
			$name = $combined[$i]['name'];
			echo "{$this->indent($indent_level+1)}\$$name" . ($i < $no_of_prefix + $no_properties - 1 ? "," : "") . "\n";
		}
		echo "{$this->indent($indent_level)})\n";
		echo "{$this->indent($indent_level)}{\n";
		if (!empty($prefix_parameters)) {
			echo "{$this->indent($indent_level+1)}parent::__construct(\n";
			for ($i = 0; $i < $no_of_prefix; $i++) {
				$name = $prefix_parameters[$i]['name'];
				echo "{$this->indent($indent_level+2)}\$$name" . ($i < $no_of_prefix - 1 ? "," : "") . "\n";
			}
			echo "{$this->indent($indent_level+1)});\n";
		}
		for ($i = 0; $i < $no_properties; $i++) {
			$name = $required_properties[$i]['name'];
			echo "{$this->indent($indent_level+1)}if (is_null(\$$name)) throw new \InvalidArgumentException(\"Parameter '$name' must not be null\");\n";
			echo "{$this->indent($indent_level+1)}\$this->$name = \$$name;\n";
		}
		echo "{$this->indent($indent_level)}}\n";
		return ob_get_clean();
	}
	
	private function generateStaticMakeParametersDocBlock($indent_level, $prefix_parameters, $required_properties)
  {
		ob_start();
		echo "\n";
		echo "{$this->indent($indent_level)}/**\n";
		foreach ($prefix_parameters as $property) {
			echo "{$this->indent($indent_level)} * @param {$property['type']} \${$property['name']}\n";
		}
		foreach ($required_properties as $property) {
			echo "{$this->indent($indent_level)} * @param {$property['type']} \${$property['name']}\n";
		}
		echo "{$this->indent($indent_level)} *\n";
		echo "{$this->indent($indent_level)} * @return self\n";
		echo "{$this->indent($indent_level)} **/\n";
		return ob_get_clean();
	}
	
	private function generateStaticMake($indent_level, $prefix_parameters, $required_properties)
  {
		$required_properties = array_merge($prefix_parameters, $required_properties);
		$no_properties = count($required_properties);
		ob_start();
		echo "{$this->indent($indent_level)}public static function make(\n";
		for ($i = 0; $i < $no_properties; $i++) {
			$name = $required_properties[$i]['name'];
			echo "{$this->indent($indent_level+1)}\$$name" . ($i < $no_properties - 1 ? "," : "") . "\n";
		}
		echo "{$this->indent($indent_level)})\n";
		echo "{$this->indent($indent_level)}{\n";
		echo "{$this->indent($indent_level+1)}return new self(\n";
		for ($i = 0; $i < $no_properties; $i++) {
			$name = $required_properties[$i]['name'];
			echo "{$this->indent($indent_level+2)}\$$name" . ($i < $no_properties - 1 ? "," : "") . "\n";
		}
		echo "{$this->indent($indent_level+1)});\n";
		echo "{$this->indent($indent_level)}}\n";
		return ob_get_clean();
	}
	
	private function generateOptionalCode($indent_level, $optional_properties)
  {
		ob_start();
		echo "{$this->indent($indent_level)}//region Fluent setters\n";
		foreach ($optional_properties as $property) {
			echo $this->generateFluentSetter($indent_level, $property);
		}
		echo "{$this->indent($indent_level)}//endregion\n";
		return ob_get_clean();
	}
	
	private function generateFluentSetter($indent_level, $property)
	    {
		ob_start();
		$name = $property['name'];
		echo "\n";
		echo "{$this->indent($indent_level)}/**\n";
		echo "{$this->indent($indent_level)} * @param {$property['type']} \${$name}\n";
		echo "{$this->indent($indent_level)} *\n";
		echo "{$this->indent($indent_level)} * @return \$this\n";
		echo "{$this->indent($indent_level)} **/\n";
		echo "{$this->indent($indent_level)}function " . camel_case("set_$name") . "(\${$name})\n";
		echo "{$this->indent($indent_level)}{\n";
		echo "{$this->indent($indent_level+1)}\$this->$name = \$$name;\n";
		echo "{$this->indent($indent_level+1)}return \$this;\n";
		echo "{$this->indent($indent_level)}}\n";
		return ob_get_clean();
	}
	
	private function generateGetters($indent_level, $properties)
  {
		ob_start();
		echo "{$this->indent($indent_level)}//region Getters\n";
		foreach ($properties as $property) {
			echo $this->generateGetter($indent_level, $property);
		}
		echo "{$this->indent($indent_level)}//endregion\n";
		return ob_get_clean();
	}
	
	private function generateGetter($indent_level, $property)
  {
		ob_start();
		$name = $property['name'];
		echo "\n";
		echo "{$this->indent($indent_level)}/**\n";
		echo "{$this->indent($indent_level)} * @return {$property['type']}\n";
		echo "{$this->indent($indent_level)} **/\n";
		echo "{$this->indent($indent_level)}function " . camel_case("get_$name") . "()\n";
		echo "{$this->indent($indent_level)}{\n";
		echo "{$this->indent($indent_level+1)}return \$this->$name;\n";
		echo "{$this->indent($indent_level)}}\n";
		return ob_get_clean();
	}
	
	private function injectGeneratedCodeIntoClass($path, $inject_code)
  {
		$code = file_get_contents($path);
		$section = self::SECTION_NAME;
		
		if (strpos($code, "// region $section") !== false) {
			$start_part = "//\s*region " . preg_quote($section);
			$end_part = "^\s*//\s*endregion " . preg_quote($section);
			$code = preg_replace_callback("#($start_part).*?($end_part)#ism", function ($m) use ($inject_code) {
				return $m[1] . "\n" . $inject_code . $m[2];
			}
			, $code);
		}
		else {
			$code = preg_replace_callback("#([\n\r]+)(\};?\s*$)#is", function ($m) use ($inject_code, $section) {
				return (
          $m[1] .
          "{$this->indent(1)}// region $section\n" .
          $inject_code .
          "{$this->indent(1)}// endregion $section\n" .
          $m[2]
        );
			}
			, $code);
		}
		
		file_put_contents($path, $code);
	}
	// 	endregion
}
