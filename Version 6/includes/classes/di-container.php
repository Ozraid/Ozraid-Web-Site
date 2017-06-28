<?php
namespace Ozraid\Includes\Classes;

/**
 * Dependency Injection (DI) Container class.
 *
 * Type:         Class
 * Dependencies: NULL
 * Description:  Loads, initialises (injecting constructor dependencies), stores, and returns PHP classes.
 *               PHP class dependencies are stored in and loaded from a JSON file.
 *               Variables can be stored in the DI Container class to be used as dependencies.
 * Version:      6.0.0
 * Author:       Sethco
 * Licence:      Freeware
 *
 * Notes:        Public class methods:
 *                - __set()            Magic method that stores a dependency.
 *                - __get()            Magic method that returns a dependency, or NULL.
 *                - __isset()          Magic method that checks if a variable is stored as a dependency or in $this->objects property.
 *                - set_dependencies() Maps and stores PHP class dependencies and JSON files in $this->json_pathname file.
 */

// Loads and initialises Status Error class and exception handler that blocks access to the PHP file if the 'AB_PATH' constant is undefined.
require_once $_SERVER['DOCUMENT_ROOT'] .'/includes/classes/status-error.php';

class Di_Container {
	
	/**
	 * PRIVATE PROPERTIES
	 *
	 * @var object $dependencies  Dependency injection map. {
	 *   @type object Class name. {
	 *     @type string path         => Relative path to the class 'php' file.
	 *     @type string type         => Dependency type. 'Class' or 'Singleton Class' values.
	 *     @type string class        => Namespace and class name.
	 *     @type var    dependencies => NULL or array of dependency names to be injected.
	 *   }
	 *   @type object JSON file name. {
	 *     @type string path         => Relative path to the 'json' file.
	 *     @type string type         => Dependency type. 'JSON' value.
	 *   }
	 * }
	 * @var object $object        Classes, JSON file contents, or variables. {
	 *   @type object class name     => Class.
	 *   @type object JSON file name => JSON file contents.
	 *   @type var    variable name  => Variable.
	 * }
	 * @var string $json_pathname Relative path to the JSON PHP class dependencies map file.
	 */
	private $dependencies;
	private $objects;
	private $json_pathname;
	
	/**
	 * Class constructor.
	 *
	 *  1. Stores the relative path to the JSON PHP class dependencies map file in $this->json_pathname property.
	 *  2. If $set exists or 'dependencies.json' does not exist, calls $this->set_dependencies() method.
	 *  3. Loads and stores the contents of $this->json_pathname file as an object in $this->dependencies property.
	 *  4. Sets $this->objects property as a storage class.
	 *  5. Stores DateTime class in $this->objects property.
	 *
	 * @param var $set NULL or any value.
	 */
	public function __construct( $set = NULL ) {
		$this->json_pathname = constant ( 'AB_PATH' ) .'json/dependencies.json';
		if ( $set !== NULL || ! file_exists ( $this->json_pathname ) ) {
			$this->set_dependencies();
		}
		$this->dependencies = json_decode ( file_get_contents ( $this->json_pathname ) );
		$this->objects = new \stdClass();
		$config = $this->Config;
		$this->DateTime = new \DateTime( $time = 'now', new \DateTimeZone( $config->timezone ) );
	}
	
	/**
	 * Magic method that stores a dependency.
	 *
	 * @param string $name  Variable name.
	 * @param var    $value Variable.
	 */
	public function __set( $name, $value ) {
		$this->objects->{$name} = $value;
	}
	
	/**
	 * Magic method that returns a dependency, or NULL.
	 *
	 *  1. Returns a variable if it exists in $this->objects.
	 *  2. If $name does not exist:
	 *      - Calls $this->set_object() method
	 *      - Returns the variable if it is stored in $this->objects
	 *      - Returns NULL if the variable does not exist.
	 *
	 * @param string $name Variable name.
	 * @return var Variable.
	 */
	public function __get( $name ) {
		if ( isset ( $this->objects->{$name} ) ) {
			return $this->objects->{$name};			
		}
		else {
			$this->set_object( $name );
			if ( isset ( $this->objects->{$name} ) ) {
				return $this->objects->{$name};			
			}
			else {
				return NULL;
			}
		}
	}
	
	/**
	* Magic method that checks if a variable is stored as a dependency or in $this->objects property.
	*
	* @param string $name Class name, JSON file name, or variable name.
	* @return TRUE if $name exists in $this->objects or $this->dependencies properties, otherwise FALSE.
	*/
	public function __isset( $name ) {
		if ( isset ( $this->objects->{$name} ) || isset ( $this->dependencies->{$name} ) ) {
			return TRUE;
		}
			return FALSE;
	}
	
	/**
	 * Maps and stores PHP class dependencies and JSON files in $this->json_pathname file.
	 *
	 * @var string $json_path      Relative path to the directory where JSON files are stored.
	 * @var array  $regex          PHP file regular expressions. {
	 *   @type string php class dependency variable => Regular expression used to match data in a 'php' class file.
	 * }
	 * $var array  $mvc_namespaces  Model, View, and Controller namespaces.
	 * $var array  $mvc_class_names Model, View, and Controller class names.
	 * @var object $directories     Recursive DirectoryIterator class listing all directories and files in the 'AB_PATH' directory
	 *                              and sub-directories.
	 * @var array  $dependencies    PHP class and JSON file dependencies. See $this->dependencies property.
	 * @var string $class_name      PHP class name.
	 * @var string $file_name       JSON file name without '.json' extension.
	 */
	public function set_dependencies() {
		// Method variables.
		$regex = array (
			'namespace'    => '/\nnamespace\s(.*);\n/',
			'type'         => '/\n\s\*\sType:\s*(.*)\n/',
			'dependencies' => '/\n\s\*\sDependencies:\s*(.*)\n/'
		);
		$mvc_namespaces = array ( constant ( 'BASE_NS' ) .'\Models', constant ( 'BASE_NS' ) .'\Controllers', constant ( 'BASE_NS' ) .'\Views' );
		$mvc_class_names = array ( 'Model', 'Controller', 'View' );
			$directories = new \RecursiveIteratorIterator( new \RecursiveDirectoryIterator( constant ( 'AB_PATH' ) ), \RecursiveIteratorIterator::SELF_FIRST );
			
		// Iterates through $directory object.
		foreach ( $directories as $object ) {
			if ( $object->isFile() ) {
				// Opens and reads PHP class files, and stores PHP classes in $dependencies.
				if ( $object->getExtension() == 'php' ) {
					$file = fopen ( $object->getPathname(), 'r'  );
					$contents = fread ( $file, $object->getSize() );
					preg_match ( '/\nclass\s([a-zA-Z0-9_]*)(?:\s.*\{.*\n|.*\n.*\{)/', $contents, $matches );
					if ( $matches[1] ) {
						$class_name = $matches[1];
						foreach ( $regex as $variable => $pattern ) {
							preg_match ( $pattern, $contents, $matches );
							if ( $matches[1] ) {
								$class[$variable] = trim ( $matches[1] );
							}
						}
						if ( ! isset ( $class['type'] ) ) {
							$class['type'] = 'Class';
						}
						$class['class'] = $class['namespace'] .'\\' .$class_name;
						if ( in_array ( $class['namespace'], $mvc_namespaces ) ) {
							if ( ! in_array ( $class_name, $mvc_class_names ) ) {
								$class_name = str_replace ( 's\\', '_', ltrim ( $class['class'], constant ( 'BASE_NS' ) .'\\' ) );
							}
						}
						$dependencies[$class_name] = array (
							'path'  => $object->getPathname(),
							'type'  => $class['type'],
							'class' => $class['class']
						);
						if ( ! isset ( $class['dependencies'] ) || $class['dependencies'] == 'NULL' ) {
							$dependencies[$class_name]['dependencies'] = NULL;
						}
						else {
							$dependencies[$class_name]['dependencies'] = explode ( ',', str_replace ( ' ', '', $class['dependencies'] ) );
						}
					}
					fclose( $file );
				}
				// Stores JSON files in $dependencies.
				else if ( $object->getExtension() == 'json' && $object->getFilename() != 'dependencies.json' ) {
					$file_name = str_ireplace ( '.json', '', $object->getFilename() );
					$dependencies[$file_name] = array (
						'path' => $object->getPathname(),
						'type' => 'JSON'
					);
				}
				unset( $class, $matches, $class_name, $file_name );
			}
		}
		
		// Sorts $dependencies
		if ( isset ( $dependencies ) && is_array ( $dependencies ) ) {
			ksort ( $dependencies );
		}
		
		// Creates the directory where $this->json_pathname file is stored if it does not exist.
		preg_match ( '/^(.*)\/[^\/]+\.(?:json|JSON)$/', $this->json_pathname, $path_matches );
		$json_path = $path_matches[1];
		if ( ! file_exists ( $json_path ) ) {
			mkdir ( $json_path, 0755 );
		}
		
		// Stores $dependencies as a JSON object in $this->json_pathname file.
		$file = fopen ( $this->json_pathname, 'w' );
		fwrite ( $file, json_encode ( $dependencies, JSON_FORCE_OBJECT ) );
		fclose ( $file );
	}
	
	/**
	 * Loads a variable's file, and stores the variable in $this->objects.
	 *
	 *  1. Loads and initialises a PHP class, and stores the class in $this->objects property.
	 *  2. Loads and stores the contents of a JSON file as an object in $this->objects property.
	 *  3. Returns if $name does not exist in $this->dependencies, or is not a PHP class or JSON file.
	 *
	 * The // require_once lines have been commented as a class autoloader in the Front Controller exists.
	 *
	 * @param string $name PHP class name, or JSON file name without '.json' extension.
	 */
	private function set_object( $name ) {
		if ( isset( $this->dependencies->{$name} ) ) {
			switch ( $this->dependencies->{$name}->type ) {
				case 'Class' :
					// require_once $this->dependencies->{$name}->path;
					$this->set_instance( $name );
					break;
				case 'Singleton Class' :
					// require_once $this->dependencies->{$name}->path;
					$this->set_singleton_class( $name );
					break;
				case 'JSON' :
					$this->objects->{$name} = json_decode ( file_get_contents ( $this->dependencies->{$name}->path ) );
					break;
				default:
					break;
			}
		}
	}
	
	/**
	 * Initialises an instance of a singleton class and stores it in $this->objects property.
	 *
	 * @param string $name PHP class name (without namespace).
	 */
	private function set_singleton_class( $name ) {
		$class = $this->dependencies->{$name}->class .'::get_instance';
		$this->objects->{$name} = $class( $this->set_arguments( $name ) );
	}
	
	/**
	 * Initialises an instance of a class and stores it in $this->objects property.
	 *
	 * @param string $name PHP class name (without namespace).
	 */
	private function set_instance( $name ) {
		$class = $this->dependencies->{$name}->class;
		$this->objects->{$name} = new $class( $this->set_arguments( $name ) );
	}
	
	/**
	 * Returns class dependencies to be injected, or NULL.
	 *
	 * 1. Adds the dependency to $args if it exists in $this->objects property
	 * 2. Tries to load and initialise the dependency if it does not exist in $this->objects property.
	 * 3. Returns $args or NULL if no dependencies for the class exist.
	 *
	 * @var object $args Constructor argument variables.
	 *
	 * @param string $name PHP class name (without namespace).
	 * @return var Object containing constructor argument variables or NULL.
	 */
	private function set_arguments( $name ) {
		if ( isset ( $this->dependencies->{$name}->dependencies ) ) {
			$args = new \stdClass();
			foreach ( $this->dependencies->{$name}->dependencies as $dependency ) {
				$args->{$dependency} = $this->{$dependency};
			}
			return $args;
		}
		else {
			return NULL;
		}
	}
	
}
