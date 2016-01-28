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
class Dimension extends Component
{
    protected $_value;
    protected $_unit;

    protected $_units = [
        'mm'=>'milimeters',
        'cm'=>'centimeters',
        'm'=>'meters',
        'in'=>'inches',
    ];

    public function units() {
        return $this->_units;
    }

    public function __construct($value=null, $unit=null)
    {
        if(null == $unit) $unit = Config::get('default.dimensions.unit');
        $this->unit = $unit;
        $this->value= $this->convert($value,$unit,'mm');
    }

    public function getValue()
    {
        return $this->_value;
    }
    public function setValue($val)
    {
        $this->_value = $val;
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

        //first convert to mm
        $value = 0;
        switch($from) {
        case 'mm': $value = $value; break;
        case 'cm': $value = $value/10; break;
        case 'm': $value = $value/100/10; break;
        case 'in': $value = $value/3.2808/100/10; break;
        }

        //then convert to destination unit
        switch($to) {
        case 'mm': (float)number_format($value = $value,3,localeconv()['decimal_point'],''); break;
        case 'cm': (float)number_format($value = $value*10,3,localeconv()['decimal_point'],''); break;
        case 'm': (float)number_format($value = $value*10*100,3,localeconv()['decimal_point'],''); break;
        case 'in': (float)number_format($value = $value*10*100*3.2808,3,localeconv()['decimal_point'],''); break;
        }

        return $value;
    }

    public function __call($name,$args) {
        if(substr($name,0,2) == 'in') {
            $unit = substr($name,2); 
            return $this->convert($this->value,'mm',$unit);
        }
        throw new \Exception('No such function "'.$name.'"');
    }

}
