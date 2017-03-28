<?php
namespace Carawebs\SageBladeData;

/**
*
*/
abstract class Controller implements \ArrayAccess
{
    public $data = [];

    public function __construct($postMeta = NULL)
    {
        $this->postMeta = $postMeta ?? NULL;
        $this->setData();
        $this->setTargetViews();
        $this->returnDataToBlade();
    }

    /**
     * A method that returns an array of data to be passed to the specified views.
     */
    abstract public function dataToReturn();

    /**
     * A method that returns an array of target templates (views) - specified by
     * the relevant body class.
     *
     * @return array Array of views that should recieve data.
     */
    abstract public function targetTemplates();

    /**
     * Set data that will be returned to the view.
     *
     * We can do `$this[$key] = $value` because the class implements the
     * ArrayAccess interface.
     */
    public function setData()
    {
        $reflect = new \ReflectionClass($this);
        $class = lcfirst($reflect->getShortName());
        $data = $this->dataToReturn();
        foreach ($data as $key => $value) {
            $key = $class.ucfirst($key);
            $this[$key] = $value;
        }
    }

    /**
     * Set an array of target templates - these are the templates that will have
     * access to the data.
     */
    public function setTargetViews()
    {
        $this->targetViews = $this->targetTemplates();
    }

    /**
    * Return the data to the blade view.
    *
    * @return array Data built using this class
    */
    public function returnDataToBlade()
    {
        foreach ($this->targetViews as $view) {
            add_filter('carawebs/template/'.$view.'/data', function($data) {
                return $this->data;
            });
        }
    }

    public function offsetExists ($offset) {
        return isset($this->data[$offset]);
    }

    public function offsetGet ($offset) {
        return isset($this->data[$offset]) ? $this->data[$offset] : null;
    }

    public function offsetSet ($offset, $value) {
        if (is_null($offset)) {
            $this->data[] = $value;
        } else {
            $this->data[$offset] = $value;
        }
    }

    public function offsetUnset ($offset) {
        unset($this->data[$offset]);
    }
}
