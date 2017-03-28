<?php
namespace Carawebs\SageBladeData;

/**
*
*/
abstract class Controller
{
    public function __construct(CommonData $dataObject, $postMeta = NULL)
    {
        $this->dataObject = $dataObject;
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
     * We can do `$this->dataObject[$key] = $value` because this object implements
     * the ArrayAccess interface.
     */
    public function setData()
    {
        $reflect = new \ReflectionClass($this);
        $class = lcfirst($reflect->getShortName());
        $data = $this->dataToReturn();

        foreach ($data as $key => $value) {
            $key = $class.ucfirst($key);
            $this->dataObject[$key] = $value;
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
                return $this->dataObject->data;
            });
        }
    }
}
