<?php
namespace Carawebs\SageBladeData;

/**
 * A class that is used to hold data.
 *
 * An instance of this class is passed to controllers as they are instantiated.
 * This means that as we loop through controllers, the data for a specific view
 * is added to the CommonData object. This allows different controllers to pass
 * data to a single view.
 */
class CommonData implements \ArrayAccess
{
    /**
     * Publicly accessible data to be passed to views.
     * @var array
     */
    public $data = [];

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
