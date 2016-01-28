<?php

namespace Kily\Delivery;

use Kily\Delivery\Base\Component;
use Kily\Delivery\Provider\Provider;
use Kily\Delivery\Service\Service;
use Kily\Delivery\Service\ServiceFactory;
use Kily\Delivery\Model\Address;
use Kily\Delivery\Model\RawAddress;

class BaseDelivery extends Component
{
    protected $_provider = null;

    protected $_from = null;
    protected $_to = null;
    protected $_options = [];

    public function __construct(Provider $p, $from = null, $to = null, $service_codes = null,  $options = [])
    {
        $this->provider = $p;

        if ($from) {
            $this->from = $from;
        }

        if ($to) {
            $this->to = $to;
        }

        if ($service_codes) {
            $this->services = $service_codes;
        }

        if ($options) {
            $this->options = $options;
        }
    }

    public function setProvider(Provider $val)
    {
        $this->_provider = $val;
    }

    public function getProvider()
    {
        return $this->_provider;
    }

    public function setServices($val)
    {
        if (is_string($val)) {
            $val = Service::parseServicesStr($val);
        } else {
            $val = array_unique(array_filter((array) $val));
        }
        unset($this->provider->services);
        foreach ($val as $service) {
            $subservices = [];
            if(is_array($service)) {
                list($service,$subservices) = $service; 
            } elseif(is_string($service)) {
                $service = Service::parseServicesStr($service);
                if(!$service) {
                    throw new NotService;
                }
                list($service,$subservices) = $service[0];
            }
            $service = $service instanceof Service ? $service : ServiceFactory::factory($service,$subservices);
            $this->provider->addService($service);
        }
    }

    public function getServices($id = null)
    {
        if ($id) {
            return ServiceFactory::factory($id);
        }

        return $this->provider->services;
    }

    public function setOptions($val)
    {
        $this->provider->setOptions($val);
    }

    public function getOptions()
    {
        return $this->provider->getOptions();
    }

    public function getFrom() {
        return $this->_from;
    }

    public function setFrom($val) {
        if(!$val instanceof Address) {
            $val = new RawAddress($val);
        }
        $this->_from = $val;
    }

    public function getTo() {
        return $this->_to;
    }

    public function setTo($val) {
        if(!$val instanceof Address) {
            $val = new RawAddress($val);
        }
        $this->_to = $val;
    }
}
