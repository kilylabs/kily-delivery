<?php

namespace Kily\Delivery;

use DVDoug\BoxPacker\Packer as PackerPacker;

class Packer
{
    protected static $items = array();

    public static function addItem($id, $name, $qnt, $w, $h, $l, $weight)
    {
        $item = compact('id', 'name', 'qnt', 'w', 'h', 'l', 'weight');
        self::$items[] = $item;

        return count(self::$items)-1;
    }

    public static function removeItem($id)
    {
        unset(self::$items[$id]);
    }

    public static function clear()
    {
        self::$items = array();
    }

    public static function getPackMetrics()
    {
        $max_w = $max_h = $max_l = 0;
        foreach(self::$items as $item) {
            if($item['w'] > $max_w) $max_w = $item['w'];
            if($item['h'] > $max_h) $max_h = $item['h'];
            if($item['l'] > $max_l) $max_l = $item['l'];
        }   

        $delta_w = round($max_w/2);
        $delta_h = round($max_h/2);
        $delta_l = round($max_l/2);

        $try = 0;

        do {

            $try++;

            $packer = new PackerPacker();
            //$packer->setLogger(new Katzgrau\KLogger\Logger('/tmp/packer/', \Psr\Log\LogLevel::DEBUG));
            $box = new Packer\Box('Some box', $max_w+$delta_w, $max_h+$delta_h, $max_l+$delta_l);
            $packer->addBox($box);
            try {
                foreach (self::$items as $id=>$item) {
                    foreach (range(1, $item['qnt']) as $q) {
                        $packer->addItem(new Packer\Item($item['name'].' q'.$q, $item['w'], $item['h'], $item['l'], $item['weight']));
                    }
                }

                $pb = $packer->pack();

                if($pb->count() > 1) {
                    $delta_h += $max_h;
                    continue;
                }

                $pb = $pb->getIterator()->current();
                if(!$pb)
                    return array(0,0,0,0);

                return array($pb->getUsedWidth(),$pb->getUsedDepth(),$pb->getUsedLength(),round($pb->getWeight()/1000, 3));
            } catch(Exception $e) { 
                $delta_w += round($delta_w/2);
                $delta_h += round($delta_h/2);
                $delta_l += round($delta_l/2);
                continue;
            }

        } while($try < 10);

        $packer = new PackerPacker();

        $box = new Packer\Box('Unlimited box', 100000, 100000, 100000);

        $packer->addBox($box);
        foreach (self::$items as $id=>$item) {
            foreach (range(1, $item['qnt']) as $q) {
                $packer->addItem(new YadeliveryItem($item['id'].' q'.$q, $item['w'], $item['h'], $item['l'], $item['weight']));
            }
        }

        $pb = $packer->pack();
        $pb = $pb->current();

        return array($pb->getUsedWidth(),$pb->getUsedDepth(),$pb->getUsedLength(),round($pb->getWeight()/1000, 3));
    }
}
