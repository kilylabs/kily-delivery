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
class AdminLevel
{
    protected $_level;
    protected $_name;
    protected $_code;

    public function __construct($level=null, $name=null, $code=null)
    {
        $this->setLevel($level)
            ->setName($name)
            ->setCode($code);
    }

    public function getLevel()
    {
        return $this->_level;
    }
    public function setLevel($val)
    {
        $this->_level = $val;
        return $this;
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
