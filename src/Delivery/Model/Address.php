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

/**
 * @author Alexander Bogdanov <ab@dev.kily.ru>
 */
class Address extends Component
{
    protected $_coordinates;
    protected $_bounds = [];
    protected $_streetNumber;
    protected $_streetName;
    protected $_subLocality;
    protected $_locality;
    protected $_postalCode;
    protected $_adminLevels = [];
    protected $_country;
    protected $_timezone;
    protected $_flat;


    /**
     * Returns an array of coordinates (latitude, longitude).
     *
     * @return Coordinates
     */
    public function getCoordinates()
    {
        return $this->_coordinates;
    }

    /**
     * Sets the coordinates. 
     *
     * @param mixed $value Coordinates to be set. Can be string (coordinates divied by ";"), 2-elements array or Coordinates object
     * @return static
     *
     * @assert ("1.1;2.2") == $this->object
     * @assert ([1.1,2.2]) == $this->object
     * @assert (new Coordinates("1.1","2.2")) == $this->object
     */
    public function setCoordinates($value)
    {
        if(!$value instanceof Coordinates) {
            $value = new Coordinates($value);
        }
        $this->_coordinates = $value;
        return $this;
    }

    /**
     * Returns latitude
     *
     * @return float
     */
    public function getLatitude()
    {
        if (null === $this->getCoordinates()) {
            return null;
        }

        return $this->getCoordinates()->getLatitude();
    }

    /**
     * Sets the latitude. 
     *
     * @param mixed $value The latitude value (string or double)
     * @return static
     *
     * @assert ("1.1") == $this->object
     * @assert (1.22) == $this->object
     * @assert ("ololo") throws Kily\Delivery\Exception\BadValue
     */
    public function setLatitude($value)
    {
        if (null === $this->getCoordinates()) {
            $this->setCoordinates(new Coordinates);
        }
        $this->getCoordinates()->setLatitude($value);
        return $this;
    }

    /**
     * Returns longitude
     *
     * @return float
     */
    public function getLongitude()
    {
        if (null === $this->getCoordinates()) {
            return null;
        }

        return $this->getCoordinates()->getLongitude();
    }

    /**
     * Sets the longitude. 
     *
     * @param mixed $value The longitude value (string or double)
     * @return static
     *
     * @assert ("1.1") == $this->object
     * @assert (1.22) == $this->object
     * @assert ("ololo") throws Kily\Delivery\Exception\BadValue
     */
    public function setLongitude($val)
    {
        if (null === $this->getCoordinates()) {
            $this->setCoordinates(new Coordinates);
        }
        $this->getCoordinates()->setLongitude($val);
        return $this;
    }

    /**
     * Returns area bounds. This used to backcompatability with Geocoder
     *
     * @return Bounds
     *
     * @assert ("ololo") throws Kily\Delivery\Exception\BadValue
     */
    public function getBounds()
    {
        return $this->_bounds;
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
    public function setBounds($val)
    {
        $this->_bounds = $val;
        return $this;
    }

    public function getStreetNumber()
    {
        return $this->_streetNumber;
    }
    public function setStreetNumber($val)
    {
        $this->_streetNumber = $val;
        return $this;
    }

    public function getStreetName()
    {
        return $this->_streetName;
    }
    public function setStreetName($val)
    {
        $this->_streetName = $val;
        return $this;
    }

    public function getLocality()
    {
        return $this->_locality;
    }
    public function setLocality($val)
    {
        $this->_locality = $val;
        return $this;
    }

    public function getPostalCode()
    {
        return $this->_postalCode;
    }
    public function setPostalCode($val)
    {
        $this->_postalCode = $val;
        return $this;
    }

    public function getSubLocality()
    {
        return $this->_subLocality;
    }
    public function setSubLocality($val)
    {
        $this->_subLocality = $val;
        return $this;
    }

    public function getAdminLevels()
    {
        return $this->_adminLevels;
    }
    public function setAdminLevels($val)
    {
        $this->_adminLevels = $val;
        return $this;
    }

    public function getCountry()
    {
        return $this->_country;
    }
    public function setCountry($val)
    {
        if(!$val instanceof Country) {
            $val = new Country($val);
        }
        $this->_country = $val;
        return $this;
    }

    public function getCountryCode()
    {
        if(!$this->getCountry()) {
            return null;
        }

        return $this->getCountry()->getCode();
    }

    public function setCountryCode($val)
    {
        if(!$this->getCountry()) {
            $this->setCountry(new Country);
        }

        $this->getCountry()->setCode($val);
        return $this;
    }

    public function getTimezone()
    {
        return $this->_timezone;
    }
    public function setTimezone($val)
    {
        $this->_timezone = $val;
        return $this;
    }

    public function getFlat() {
        return $this->_flat;
    }

    public function setFlat($value) {
        $this->_flat = $value;
        return $this;
    }

    public function toArray()
    {
        $adminLevels = [];
        foreach ($this->getAdminLevels() as $adminLevel) {
            $adminLevels[] = [
                'name'  => $adminLevel['name'],
                'code'  => $adminLevel['code']
            ];
        }

        return array(
            'latitude'     => $this->getLatitude(),
            'longitude'    => $this->getLongitude(),
            'bounds'       => $this->getBounds() ?: null,
            'streetNumber' => $this->getStreetNumber(),
            'streetName'   => $this->getStreetName(),
            'postalCode'   => $this->getPostalCode(),
            'locality'     => $this->getLocality(),
            'subLocality'  => $this->getSubLocality(),
            'adminLevels'  => $adminLevels,
            'country'      => $this->getCountry() ? $this->getCountry()->getName() : null,
            'countryCode'  => $this->getCountry() ? $this->getCountry()->getCode() : null,
            'timezone'     => $this->getTimezone(),
        );
    }

    /*
     * @todo need good formatter
     */
    public function getFullAddress() {
        return implode(', ',array_filter([
            $this->getPostalCode(),
            $this->getCountry() ? $this->getCountry()->getName() : null,
            $this->getAdminLevels()?implode(', ',array_column($this->getAdminLevels(),'name')):null,
            $this->getLocality(),
            $this->getStreetName(),
            $this->getStreetNumber()])
        );
    }
}
