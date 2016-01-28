<?php

namespace Kily\Delivery\Base;

use Kily\Delivery\Exception\PropertyNotDefined;
use Kily\Delivery\Exception\PropertyReadonly;

class Component
{
    public function __get($name)
    {
        $getter = 'get'.$name;
        if (method_exists($this, $getter)) {
            return $this->$getter();
        }
        throw new PropertyNotDefined('Property "'.get_class($this).'.'.$name.'" is not defined.');
    }

    public function __set($name, $value)
    {
        $setter = 'set'.$name;
        if (method_exists($this, $setter)) {
            return $this->$setter($value);
        }
        if (method_exists($this, 'get'.$name)) {
            throw new PropertyReadonly('Property "'.get_class($this).'.'.$name.'" is read only.');
        } else {
            throw new PropertyNotDefined('Property "'.get_class($this).'.'.$name.'" is not defined.');
        }
    }

    public function __isset($name)
    {
        $getter = 'get'.$name;
        if (method_exists($this, $getter)) {
            return $this->$getter() !== null;
        }

        return false;
    }

    public function __unset($name)
    {
        $setter = 'set'.$name;
        if (method_exists($this, $setter)) {
            $this->$setter(null);
        } elseif (method_exists($this, 'get'.$name)) {
            throw new PropertyReadonly('Property "'.get_class($this).'.'.$name.'" is read only.');
        }
    }
}
