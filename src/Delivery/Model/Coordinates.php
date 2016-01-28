<?php

/**
 * This file is part of the Geocoder package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Kily\Delivery\Model;

use Kily\Delivery\Base\Component;

/**
 * @author William Durand <william.durand1@gmail.com>
 */
class Coordinates extends Component
{
    protected $_latitude;
    protected $_longitude;

    public function __construct($latitude=null, $longitude=null)
    {
        $this->setLatitude($latitude);
        $this->setLongitude($longitude);
    }

    public function getLatitude()
    {
        return $this->_latitude;
    }
    public function setLatitude($val)
    {
        $this->_latitude;
        return $this;
    }

    public function getLongitude()
    {
        return $this->_longitude;
    }
    public function setLongitude($val)
    {
        $this->_longitude = $val;
        return $this;
    }
}
