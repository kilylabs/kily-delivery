<?php

namespace Kily\Delivery\Service;

use Kily\Delivery\Base\Component;
use Kily\Delivery\Exception\ShouldBeOverriden;
use Kily\Delivery\Exception\NotSupported;

class Service extends Component
{

    protected $_subservices = [];

    public function __construct($subservices=[]) {
        if($subservices) {
            $this->subservices = $subservices;
        }
    }

    public function getName()
    {
        throw new ShouldBeOverriden();
    }

    public function getSubservices() {
        return $this->_subservices;
    }

    public function setSubservices($val) {
        $this->_subservices = [];
        if($val) {
            foreach(array_unique($val) as $subservice) {
                if(in_array($subservice,$this->subservices())) {
                    $this->_subservices[] = $subservice;
                } else {
                    throw new NotSupported('"'.$subservice.'" subservice is not supported by service "'.$this->getName().'"');
                }
            }
        }
    }

    public function subservices() {
        return [];
    }

    /**
     * @todo Should use parser instead of regex
     */
    public static function parseServicesStr($str) {
        $tmpstr = $str;

        preg_match_all('/\[[0-9\w_,]*\]/i',$str,$matches);
        $subservices = [];
        if($matches && $matches[0]) {
            $subservices = $matches[0];
            foreach($matches[0] as $idx=>$match) {
                $tmpstr = substr_replace($tmpstr,'{'.$idx.'}',strpos($tmpstr,$match),strlen($match));
            }
        }
        $result = array_map(function($str){return trim(preg_replace('/\s/','',$str));},explode(',',$tmpstr));
        $tmpresult = [];
        foreach($result as $service) {
            $idx = null;
            if(preg_match('/\{(\d+)\}/',$service,$matches)) {
                $service = substr($service,0,-(strlen($matches[0])));
                $idx = $matches[1];
            }
            $tmpresult[] = [$service, ($idx !== null) ? explode(',',trim($subservices[$idx],'[]')) : []];
        }
        return $tmpresult;
    }

    public function equalsTo(Service $service) {
        return ($this->getName() == $service->getName()) && !array_diff($this->subservices,$service->subservices); 
    }

    public function equalsToServiceString($str) {
        $services = self::parseServicesStr($str);
        foreach($services as $service) {
            if($this->getName() == $service[0]) {
                if(!array_filter($service[1])) return true;
                if(!array_diff($service[1],$this->subservices)) return true;
            }
        }
        return false;
    }
}
