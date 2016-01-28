<?php

/**
 * This file is part of the Kily\Delivery package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Kily\Delivery\Model;

use Kily\Delivery\Base\Map;
use Kily\Delivery\Exception\Exception;
use Kily\Delivery\Model\Price;
use Kily\Delivery\Model\Weight;
use Kily\Delivery\Model\Dimensions;
use Kily\Delivery\Model\Position;

/**
 * @author Alexander Bogdanov <ab@dev.kily.ru>
 */
class PositionCollection extends Map
{

    protected $_items = [];

    public function __construct(array $items = []) {
        $this->items = $items;
    }

    public function getItems() {
        return $this->toArray();
    }

    public function setItems($val) {
        $items = [];
        foreach((array)$val as $item) {
            if($item instanceof Position) {
                $items[] = $item;
            } elseif(is_array($item)) {
                @list($id,$sku,$name,$price,$count,$weight,$dimensions) = $item;
                $items[] = new Position($id,$sku,$name,$price,$count,$weight,$dimensions);
            } else {
                throw new Exception('Items array can contain only Position object or array. The given thing is: '.print_r($item,true));
            }
        }
        foreach($items as $item) {
            $this->add($item->getId(),$item);
        }
        return $this;
    }

    public function getTotalCost() {
        $sum = 0;
        $currency = null;
        foreach($this->items as $item) {
            if(!$currency) $currency = $item->price->currency;
            $sum += $item->price->getPrice()*$item->getCount();
        }
        return new Price($sum,$currency);
    }

    public function getTotalWeight() {
        $sum = 0;
        foreach($this->items as $item) {
            $sum += $item->weight->getWeight()*$item->getCount();
        }
        return new Weight($sum);
    }

    public function getTotalVolume() {
        $sum = 0;
        foreach($this->items as $item) {
            $sum += $item->dimensions->getVolume()->getValue()*$item->getCount();
        }
        return new Volume($sum);
    }
}
