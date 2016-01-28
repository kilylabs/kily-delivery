<?php

namespace Kily\Delivery\Service;

use Kily\Delivery\Utils;
use Kily\Delivery\Exception\ClassNotFound;

class ServiceFactory
{
    public static function factory($id,$subservices=[])
    {
        $id = ucfirst(Utils::camelize($id));
        $class = '\\Kily\\Delivery\\Service\\'.$id;
        if (!class_exists($class)) {
            throw new ClassNotFound($class.' not found!');
        }

        return new $class($subservices);
    }
}
