<?php

namespace Kily\Delivery;

use Mascame\Arrayer\Arrayer;

class Config
{
    private static $_instance;

    private $options = [];

    public static function getInstance()
    {
        if (!self::$_instance) {
            self::$_instance = new static();
        }

        return self::$_instance;
    }

    public function __construct()
    {
        $this->_set('debug', false);
        $this->_set('default.language','ru_RU');
        $this->_set('default.locale', 'ru_RU.UTF-8');
        $this->_set('default.volume.unit', 'm3');
        $this->_set('default.dimensions.unit', 'mm');
        $this->_set('default.dimensions.format', 'whl');
        $this->_set('default.weight.unit', 'g');
        $this->_set('geocode.adapter', new \Http\Adapter\Guzzle6\Client());
        $this->_set('geocode.provider', new \Geocoder\Provider\Yandex\Yandex($this->_get('geocode.adapter')),$this->_get('default.language'));
        $this->_set('geocode.geocoder', new \Geocoder\StatefulGeocoder($this->_get('geocode.provider')));
    }

    public static function get($name)
    {
        return self::getInstance()->_get($name);
    }

    public static function set($name, $val)
    {
        return self::getInstance()->_set($name, $val);
    }

    protected function _get($name)
    {
        $c = new Arrayer($this->options);

        return $c->get($name);
    }

    protected function _set($name, $val)
    {
        $c = new Arrayer($this->options);
        $c->set($name, $val);
        $this->options = $c->getArray();
    }
}
