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
        $pathToControllers = apply_filters('carawebs-controllers/path-to-controllers', get_template_directory() . '/src/Carawebs/Controllers');
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
        foreach($this->controllerFiles as $file)
        {
            if(!is_dir($file)) {
                $namespace = str_replace('/src/', '', stristr(pathinfo($file)['dirname'], '/src/'));
                $namespace = str_replace('/', '\\', $namespace);
                $file = pathinfo($file)['filename'];
                $classes[] = $namespace . '\\' . $file;
            }
        }
        $this->classes = $classes;
        var_dump($this->classes);
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
