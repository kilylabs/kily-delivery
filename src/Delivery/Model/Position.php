<?php

/**
 * This file is part of the Kily\Delivery package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Kily\Delivery\Model;

use Kily\Delivery\Base\Component;
use Kily\Delivery\Model\Price;
use Kily\Delivery\Model\Weight;
use Kily\Delivery\Model\Dimensions;

/**
 * @author Alexander Bogdanov <ab@dev.kily.ru>
 */
class Position extends Component
{
    protected $_id;
    protected $_sku;
    protected $_name;
    protected $_price;
    protected $_weight;
    protected $_dimensions;
    protected $_count;

    public function __construct($id,$sku,$name,Price $price=null,$count=1,Weight $weight=null,Dimensions $dimensions=null) {
        $this->id = $id;
        $this->sku = $sku;
        $this->name = $name;
        $this->price = $price;
        $this->count = $count;

        if(null == $weight) {
            $weight = new Weight(0);
        }
        $this->weight = $weight;

        if(null == $dimensions) {
            $dimensions = new Dimensions('0');
        }
        $this->dimensions = $dimensions;
    }

    /**
     * Returns ID of position
     *
     * @return Coordinates
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Sets the id;
     *
     * @param mixed $value Coordinates to be set. Can be string (coordinates divied by ";"), 2-elements array or Coordinates object
     * @return static
     *
     */
    public function setId($value)
    {
        $this->_id = $value;
        return $this;
    }

    /**
     * Returns latitude
     *
     * @return float
     */
    public function getSku()
    {
        return $this->_sku;
    }

    /**
     * Sets the latitude. 
     *
     * @param mixed $value The latitude value (string or double)
     * @return static
     *
     */
    public function setSku($value)
    {
        $this->_sku = $value;
        return $this;
    }

    /**
     * Returns longitude
     *
     * @return float
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Sets the longitude. 
     *
     * @param mixed $value The longitude value (string or double)
     * @return static
     *
     */
    public function setName($value)
    {
        $this->_name = $value;
        return $this;
    }

    /**
     * Returns area bounds. This used to backcompatability with Geocoder
     *
     * @return Bounds
     *
     * @assert ("ololo") throws Kily\Delivery\Exception\BadValue
     */
    public function getPrice()
    {
        return $this->_price;
    }

    /**
     * Sets the bounds
     *
     * @param mixed $value The 4-value array or string (4 bound coordinates separated by ";") or Bounds object
     * @return static
     *
     * @assert ("1.1") == $this->object
     * @assert (1.22) == $this->object
     * @assert ("ololo") throws Kily\Delivery\Exception\BadValue
     */
    public function setPrice($val)
    {
        if(!$val instanceof Price) {
            $val = new Price($val);
        }
        $this->_price = $val;
        return $this;
    }

    public function getCount()
    {
        return $this->_count;
    }
    public function setCount($val)
    {
        $this->_count = $val;
        return $this;
    }

    public function getWeight()
    {
        return $this->_weight;
    }
    public function setWeight($val)
    {
        if(null !== $val && !($val instanceof Weight)) {
            $val = new Weight($val);
        }
        $this->_weight = $val;
        return $this;
    }

    public function getDimensions()
    {
        return $this->_dimensions;
    }
    public function setDimensions($val)
    {
        if(null !== $val && !($val instanceof Dimensions)) {
            $val = new Dimensions($val);
        }
        $this->_dimensions = $val;
        return $this;
    }
}
