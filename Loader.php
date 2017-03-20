<?php
namespace Carawebs\SageBladeData;

use Carawebs\SageBladeData\Filters;
/**
*
*/
class Loader
{

    function __construct(Filters $filters, $dataAccessor = NULL)
    {
        $this->dataAccessor = $dataAccessor;
        $this->filters = $filters;
        $this->setControllerFiles();
        $this->setControllerClasses();
        $this->loadAll();
    }

    public function setControllerFiles()
    {
        $pathToControllers = apply_filters('carawebs-controllers/path-to-controllers', get_template_directory() . '/src/Carawebs/Controllers');
        $dir = $pathToControllers . '/*';
        $this->controllerFiles = glob($dir);
    }

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
    }

    public function loadAll()
    {
        if (empty($this->controllerFiles)) return;
        foreach ($this->classes as $class) {
            new $class($this->dataAccessor);
        }
        $this->filters->init();
    }
}
