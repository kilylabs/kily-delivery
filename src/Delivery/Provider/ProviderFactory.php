<?php

namespace Kily\Delivery\Provider;

use Kily\Delivery\Utils;
use Kily\Delivery\Exception\ClassNotFound;

class ProviderFactory
{
    public static function factory($id)
    {
        $id = ucfirst(Utils::camelize($id));
        $class = '\\Kily\\Delivery\\Provider\\'.$id;
        if (!class_exists($class)) {
            throw new ClassNotFound($class.' not found!');
        }

        return new $class;
    }

    public static function listProviders() {
        return [
            'edost',
            'betap',
        ];
    }
}
