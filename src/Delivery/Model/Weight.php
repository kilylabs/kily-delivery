<?php

/**
 * This file is part of the Geocoder package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Kily\Delivery\Model;

use Kily\Delivery\Config;
use Kily\Delivery\Base\Component;
use Kily\Delivery\Exception\Exception;

/**
 * @author William Durand <william.durand1@gmail.com>
 */
class Weight extends Component
{
    protected $_weight;
    protected $_unit;

    protected $_units = [
        't'=>'tonne',
        'kg'=>'kilogramm',
        'g'=>'gramm',
        'pound'=>'pound',
    ];

    public function units() {
        return $this->_units;
    }

    public function __construct($weight=null, $unit=null)
    {
        if(null == $unit) $unit = Config::get('default.weight.unit');
        $this->unit = $unit;
        $this->weight = $this->convert($weight,$unit,'g');
    }

    public function getWeight()
    {
        return $this->_weight;
    }
    public function setWeight($val)
    {
        $this->_weight = $val;
        return $this;
    }

    public function getUnit()
    {
        return $this->_unit;
    }
    public function setUnit($val)
    {
        $val = strtolower($val);
        if(!in_array($val,array_keys($this->_units)))
            throw new Exception('The unit '.$val.' is not supported');
        $this->_unit = $val;
        return $this;
    }

    public function convert($value,$from,$to=null) {
        if(!$to) $to = $this->_unit;

        $from = strtolower($from);
        $to = strtolower($to);

        foreach([$from,$to] as $unit) {
            if(!in_array($unit,array_keys($this->_units)))
                throw new Exception('The unit '.$unit.' is not supported');
        }

        //first convert to gramms
        $value = 0;
        switch($from) {
        case 't': $value = $value*1000*1000; break;
        case 'kg': $value = $value*1000; break;
        case 'g': $value = $value; break;
        case 'po': $value = $value * 453.592; break;
        }

        //then convert to destination unit
        switch($to) {
        case 't': (float)number_format($value = $value/1000/1000,3,localeconv()['decimal_point'],''); break;
        case 'kg': (float)number_format($value = $value/1000,3,localeconv()['decimal_point'],''); break;
        case 'g': (float)$value = $value; break;
        case 'po': (float)number_format($value = $value / 453.592,3,localeconv()['decimal_point'],''); break;
        }

        return $value;
    }

    public function __call($name,$args) {
        if(substr($name,0,2) == 'in') {
            $unit = substr($name,2); 
            return $this->convert($this->weight,'g',$unit);
        }
        throw new \Exception('No such function "'.$name.'"');
    }

}
