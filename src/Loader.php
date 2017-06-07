<?php
namespace Carawebs\SageBladeData;

use Carawebs\SageBladeData\Filters;

/**
* Class to instantiate controllers.
*
* Note that by default the path to the controllers directory is
* `get_template_directory() . '/src/Carawebs/Controllers'`. This is filtered
* by 'carawebs-controllers/path-to-controllers'.
*/
class Loader
{
    /**
    * Set necessary data.
    * @param Filters $filters      Object that controls hooking data to 'template_include'.
    * @param Object  $dataAccessor (Usually) an object used to access post metadata.
    */
    function __construct(Filters $filters, $dataAccessor = NULL)
    {
        $this->dataObject = new CommonData;
        $this->dataAccessor = $dataAccessor;
        $this->filters = $filters;
        $this->setControllerFiles();
        $this->setControllerClasses();
        $this->loadAll();
    }

    /**
    * Set an array of paths to the Controller classes.
    *
    * Controllers extend the Controller abstract class and are usually located
    * within the WordPress theme. Default path to controllers is:
    * `get_template_directory() . '/src/Carawebs/Controllers'`.
    * Filtered by: 'carawebs-controllers/path-to-controllers'.
    */
    public function setControllerFiles()
    {
        $pathToControllers = apply_filters('carawebs-controllers/path-to-controllers', dirname(get_template_directory()) . '/app/Carawebs/Controllers');
        $dir = $pathToControllers . '/*';
        $this->controllerFiles = glob($dir);
    }

    /**
    * Create an array of fully-namespaced controller class names.
    */
    public function setControllerClasses()
    {
        if (empty($this->controllerFiles)) return;
        $classes = [];
        foreach($this->controllerFiles as $filePath)
        {
            if(!is_dir($filePath)) {
                $classes[] = $this->getFullyQualifiedClassName($filePath);//$namespace . '\\' . $file;
            }
        }
        $this->classes = $classes;
    }

    /**
     * Get fully qualified classname from a file path.
     *
     * Sets helper values that flag when we have found the namespace/class token
     * and need to collect the string values after them.
     *
     * @see http://php.net/manual/en/tokens.php
     * @see http://jarretbyrne.com/2015/06/197/
     * @param  string $pathToFile Full path to file
     * @return string             Fully qualified classname
     */
    private function getFullyQualifiedClassName(string $pathToFile)
    {
        $contents = file_get_contents($pathToFile);
        $namespace = $class = "";
        $getting_namespace = $getting_class = false;

        foreach (token_get_all($contents) as $token) {
            if (is_array($token) && $token[0] == T_NAMESPACE) {
                // Flag that next token will be the namespace declaration
                $getting_namespace = true;
            }

            if (is_array($token) && $token[0] == T_CLASS) {
                // Flag that next token will be the class declaration
                $getting_class = true;
            }

            if ($getting_namespace === true) {
                // If this token is a string or namespace separator append the token's value to the name of the namespace
                if(is_array($token) && in_array($token[0], [T_STRING, T_NS_SEPARATOR])) {
                    $namespace .= $token[1];
                }
                else if ($token === ';') {
                    // If the token is the semicolon, namespace declaration is complete
                    $getting_namespace = false;
                }
            }

            if ($getting_class === true) {
                // If the token is a string at this point, it's the name of the class
                if(is_array($token) && $token[0] == T_STRING) {
                    $class = $token[1];
                    break;
                }
            }
        }

        // Assemble the fully-qualified class name and return it
        return $namespace ? $namespace . '\\' . $class : $class;
    }

    /**
    * Instantiate all controller classes.
    *
    * As controller objects are created, specific data is set up and hooked to
    * 'carawebs/template/{$templateClass}/data'. This is then used to send data
    * to templates in the `$this->filters->init()` method.
    * @return void
    */
    public function loadAll()
    {
        if (empty($this->controllerFiles)) return;
        foreach ($this->classes as $class) {
            new $class($this->dataObject, $this->dataAccessor);
        }
        $this->filters->init();
    }
}
