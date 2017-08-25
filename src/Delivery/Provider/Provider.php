<?php

namespace Kily\Delivery\Provider;

use Kily\Delivery\Base\Component;
use Kily\Delivery\Exception\NotService;
use Kily\Delivery\Exception\NotSupported;
use Kily\Delivery\Exception\ShouldBeOverriden;
use Kily\Delivery\Exception\BadOption;
use Kily\Delivery\Service\Service;
use Kily\Delivery\Model\Address;
use Kily\Delivery\Model\PositionCollection;
use Kily\Delivery\Model\Person;

class Provider extends Component
{
    protected $_services = [];
    protected $_from = null;

    protected $_options = [];

    public function __construct() {
    }

    public function options() 
    {
        return [
            'items',
            'person',
            'weight',
            'dimensions',
            'width',
            'height',
            'length',
        ];
    }

    public function getServices()
    {
        return $this->_services;
    }

    public function setServices($services)
    {
        if ($services === null) {
            $this->_services = [];
        } else {
            $types = [];
            foreach ($services as $service) {
                if (!$service instanceof Service) {
                    throw new NotService(gettype($service).' is not instance of Service');
                }
                if (!$this->isSupported($service->getName(),$service->subservices)) {
                    throw new NotSupported($this->getName().' provider does not support '.$service->getName().' delivery service');
                }
                if (in_array($service->getName(), $types)) {
                    continue;
                }
                $types[] = $service->getName();
            }
            $this->_services = $services;
        }
    }

    public function addService(Service $srv)
    {
        foreach ($this->getServices() as $service) {
            if (get_class($srv) == get_class($service)) {
                return;
            }
        }
        if (!$this->isSupported($srv->getName(),$srv->subservices)) {
            throw new NotSupported($this->getName().' provider does not support '.$srv->getName().' delivery service');
        }
        $this->_services[] = $srv;
    }

    public function setFrom($val)
    {
        $this->_from = $val;
    }

    public function getFrom()
    {
        return $this->_from;
    }

    public function setOptions($val, $prevopts = [])
    {
        $options = $this->parseOptions($val,$prevopts);
        $this->_options = $options;
    }

    public function parseOptions($val,$prevopts=[]) {
        $tempopts = array();
        $opts = (object) $val;

        $options = array_merge((array)$val,(array)$prevopts);
        $opt_keys = array_keys($options);
        if($diffopts = array_diff($opt_keys,$this->options())) {
            // we ignore unknown options
            //throw new BadOption($diffopts);
        }

        if(!empty($options['items'])) {
            if(!$options['items'] instanceof PositionCollection) {
                $options['items'] = new PositionCollection($options['items']);
            }
        }

        if(!empty($options['person'])) {
            if(!$options['person'] instanceof Person) {
                $options['person'] = new Person($options['person']);
            } 
        } else {
            $options['person'] = new Person;
        }

        if(!empty($options['dimensions'])) {
            @list($w,$h,$l) = explode('x',strtolower($options['dimensions']));
            if(empty($options['width']) || !$options['width']) {
                $options['width'] = $w;
            }
            if(empty($options['height']) || !$options['height']) {
                $options['height'] = $h;
            }
            if(empty($options['length']) || !$options['length']) {
                $options['length'] = $l;
            }
        }

        return (object)array_merge(array_combine($this->options(),array_fill(0,count($this->options()),false)),$options);
    }

    public function getOptions()
    {
        return $this->_options;
    }

    public function getOption($name, $default = null)
    {
        $options = $this->getOptions();
        if (!isset($options->$name)) {
            return $default;
        }

        return $options->$name;
    }

    public function isSupported($service,$subservices=[]) {
        if(is_string($service)) {
            $service = Service::parseServicesStr($service);
            if(!$service) {
                throw new NotService;
            }
            list($service,$subservices) = $service[0];
        }
        $s_name = $service instanceof Service ? $service->getName() : $service;
        foreach($this->supports() as $s_service) {
            $s_service = Service::parseServicesStr($s_service);
            list($s_service,$s_subservices) = $s_service[0];

            if($s_service == $s_name) {
                if(!$subservices) return true;
                if($ret = array_diff($subservices,$s_subservices)) {
                    throw new NotSupported('"'.$this->getName().'" provider does not support subservice "'.implode(',',$ret).'" of service "'.$s_name.'"');
                }
                return true;
            }
        }
        return false;
    }

    protected function displayXMLError($error, $xml)
    {
        $return = $xml[$error->line - 1]."\n";
        $return .= str_repeat('-', $error->column)."^\n";

        switch ($error->level) {
        case LIBXML_ERR_WARNING:
            $return .= "Warning $error->code: ";
            break;
        case LIBXML_ERR_ERROR:
            $return .= "Error $error->code: ";
            break;
        case LIBXML_ERR_FATAL:
            $return .= "Fatal Error $error->code: ";
            break;
        }

        $return .= trim($error->message).
            "\n  Line: $error->line".
            "\n  Column: $error->column";

        if ($error->file) {
            $return .= "\n  File: $error->file";
        }

        return "$return\n\n--------------------------------------------\n\n";
    }

    public function calculate(Address $from = null, Address $to = null, $options = [], $services = [])
    {
        if ($options) {
            $this->setOptions($options);
        }

        if ($services) {
            $this->setServices($services);
        }

        if($from) {
            $this->from = $from;
        }

        return $this->calculateInternal($to);
    }
}
