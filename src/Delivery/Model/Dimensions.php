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
use Kily\Delivery\Model\Dimension;
use Kily\Delivery\Base\Component;
use Kily\Delivery\Exception\Exception;

/**
 * @author William Durand <william.durand1@gmail.com>
 */
class Dimensions extends Component
{
    protected $_width;
    protected $_height;
    protected $_deepth;
    protected $_format;
    protected $_unit;

    protected $_units = [
        'm3'=>'cubic meter',
    ];

    protected $_formats = [
        'lwh'=>'deepth,width,height',
        'whd'=>'width,height,deepth',
        'whl'=>'width,height,length',
    ];

    public function __construct($width=null, $height=null, $deepth=null, $unit=null,$format=null)
    {
        if(null == $unit) $unit = Config::get('default.dimensions.unit');
        if(null == $format) $format = Config::get('default.dimensions.format');

        if($width && is_string($width) && !$height && !$deepth) {
            list($width,$height,$deepth) = $this->parseByFormat($width,$format,$unit);
        }

        $this->width = $width;
        $this->height = $height;
        $this->deepth = $deepth;
        $this->unit = $unit;
        $this->format = $format;
    }

    public function getWidth()
    {
        return $this->_width;
    }

    public function setWidth($val)
    {
        if(!$val instanceof Dimension) {
            $val = new Dimension($val,$this->unit);
        }
        $this->_width = $val;
        return $this;
    }

    public function getHeight()
    {
        return $this->_height;
    }

    public function setHeight($val)
    {
        if(!$val instanceof Dimension) {
            $val = new Dimension($val,$this->unit);
        }
        $this->_height = $val;
        return $this;
    }

    public function getDeepth()
    {
        return $this->_deepth;
    }

    public function setDeepth($val)
    {
        if(!$val instanceof Dimension) {
            $val = new Dimension($val,$this->unit);
        }
        $this->_deepth = $val;
        return $this;
    }

    public function getFormat()
    {
        return $this->_format;
    }
    public function setFormat($val)
    {
        $val = strtolower($val);
        if(!in_array($val,array_keys($this->_formats)))
            throw new Exception('The format '.$val.' is not supported');
        $this->_format = $val;
        return $this;
    }

    public function getUnit()
    {
        return $this->_unit;
    }
    public function setUnit($val)
    {
        $val = strtolower($val);
        $units = (new Dimension)->units();
        if(!in_array($val,array_keys($units)))
            throw new Exception('The unit '.$val.' is not supported');
        $this->_unit = $val;
        return $this;
    }

    public function getVolume() {
        $width = $this->width->inM();
        $height = $this->height->inM();
        $deepth = $this->deepth->inM();

        return new Volume($width*$height*$deepth);
    }

    public function parseByFormat($string,$format,$unit) {
        switch(strtoupper($format)) {
        case 'LxWxH': @list($deepth,$width,$height) = array_map(function($d)use($unit){return new Dimension($d,$unit);},explode('x',$string)); break;
        case 'WxHxL': 
        case 'WxHxD':
        default:  @list($width,$height,$deepth) = array_map(function($d)use($unit){return new Dimension($d,$unit);},explode('x',$string)); break;
        }

        return [$width,$height,$deepth];

    }

}
