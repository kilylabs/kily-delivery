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
class Bounds extends Component
{
    protected $_south;
    protected $_west;
    protected $_north;
    protected $_east;

    public function __construct($south=null, $west=null, $north=null, $east=null)
    {
        $this->setSouth($south)
            ->setWest($west)
            ->setNorth($north)
            ->setEast($east);
    }

    public function getSouth()
    {
        return $this->_south;
    }
    public function setSouth($val)
    {
        $this->_south = $val;
        return $this;
    }

    public function getWest()
    {
        return $this->west;
    }
    public function setWest($val)
    {
        $this->_west = $val;
        return $this;
    }

    public function getNorth()
    {
        return $this->_north;
    }
    public function setNorth($val)
    {
        $this->_north = $val;
        return $this;
    }

    public function getEast()
    {
        return $this->_east;
    }

    public function setEast($val)
    {
        $this->_east = $val;
        return $this; 
    }

    public function isDefined()
    {
        return !empty($this->south) && !empty($this->east) && !empty($this->north) && !empty($this->west);
    }

    public function toArray()
    {
        return [
            'south' => $this->getSouth(),
            'west'  => $this->getWest(),
            'north' => $this->getNorth(),
            'east'  => $this->getEast()
        ];
    }
}
