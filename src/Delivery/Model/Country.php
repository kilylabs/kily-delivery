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
class Country extends Component
{
    protected $_name;
    protected $_code;

    public function __construct($name=null, $code=null)
    {
        $this->setName($name)->setCode($code);
    }

    public function getName()
    {
        return $this->_name;
    }
    public function setName($val)
    {
        $this->_name = $val;
        return $this;
    }

    public function getCode()
    {
        return $this->_code;
    }
    public function setCode($val)
    {
        $this->_code = $val;
        return $this;
    }

    public function toString()
    {
        return $this->getName();
    }

}
