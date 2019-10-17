<?php

namespace Kily\Delivery\Packer;

use DVDoug\BoxPacker\Item as PackerItem;

class Item implements PackerItem
{
    protected $name;
    protected $w;
    protected $l;
    protected $d;
    protected $weight;
    protected $volume;
    protected $keep_flat = false;

    /*
    public function print()
    {
        echo implode("x", [$this->w,$this->l,$this->d]),"(",$this->getVolume(),"m3)","\n";
    }
     */

    public function __construct($name, $w, $l, $d, $weight, $volume=null, $keep_flat=false)
    {
        $this->name = $name;
        $this->w = $w;
        $this->l = $l;
        $this->d = $d;
        $this->weight = $weight;
        $this->volume = $volume ?: $this->getVolume();
        $this->keep_flat = $keep_flat;
    }

    public function getDescription(): string
    {
        return $this->name;
    }

    public function getWidth(): int
    {
        return $this->w;
    }

    public function getLength(): int
    {
        return $this->l;
    }

    public function getDepth(): int
    {
        return $this->d;
    }

    public function getWeight(): int
    {
        return $this->weight;
    }

    public function getVolume(): int
    {
        return $this->volume ?: round($this->w*$this->l*$this->d/pow(10, 9), 5);
    }

    public function getKeepFlat(): bool
    {
        return $this->keep_flat;
    }
}

