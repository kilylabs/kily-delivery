<?php

/**
 * This file is part of the Geocoder package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Kily\Delivery\Model;

use Kily\Delivery\Utils;
use Kily\Delivery\Exception\AddressNotFound;

/**
 * @author William Durand <william.durand1@gmail.com>
 */
class GeocodedAddress extends RawAddress
{
    public function __construct($raw_address=null) {
        parent::__construct($raw_address);
        if($this->rawAddress) {
            try {
                $result = Utils::addrFromString($this->rawAddress);
            } catch(\Geocoder\Exception\Exception $e) {
                throw new AddressNotFound('Error trying to geocode address "'.$this->rawAddress.'": '.$e->__toString());
            }
            foreach($result->toArray() as $k=>$v) {
                if($k == 'providedBy') continue;
                if($k == 'country') {
                    if(!$country = $this->getCountry()) {
                        $this->setCountry($country = new Country);
                    }
                    $country->setName($v);
                    continue;
                }
                $method = 'set'.ucfirst($k);
                $this->$method($v);
            }
        }
    }

    public function getFullAddress() {
        return parent::getFullAddress()?:$this->getRawAddress();
    }

}
