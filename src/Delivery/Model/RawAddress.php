<?php

/**
 * This file is part of the Geocoder package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Kily\Delivery\Model;

/**
 * @author William Durand <william.durand1@gmail.com>
 */
class RawAddress extends Address
{
    protected $_raw_address;

    public function __construct($raw_address=null) {
        $this->rawAddress = $raw_address;
    }

    public function getRawAddress() {
        return $this->_raw_address;
    }
    public function setRawAddress($val) {
        $this->_raw_address = $val;
        return $this;
    }

    public function getFullAddress() {
        return $this->rawAddress;
    }
}
