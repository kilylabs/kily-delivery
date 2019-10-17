<?php

namespace Kily\Delivery\Packer;

use DVDoug\BoxPacker\Box as PackerBox;

class Box implements PackerBox
{
    protected $ref;
    protected $outerWidth;
    protected $outerLength;
    protected $outerDepth;
    protected $innerWidth;
    protected $innerLength;
    protected $innerDepth;
    protected $innerVolume;
    protected $emptyWeight;
    protected $maxWeight = 1000000;

    /*
    public function print()
    {
        echo implode("x", [$this->innerWidth,$this->innerLength,$this->innerDepth]),"(",$this->getInnerVolume(),"m3)","\n";
    }
     */

    public function __construct($ref, $ow, $ol, $od, $iw=null, $il=null, $id=null, $iv=null, $ew=null, $mw=null)
    {
        $this->ref = $ref;
        $this->outerWidth = $ow;
        $this->outerLength = $ol;
        $this->outerDepth = $od;
        $this->innerWidth = $iw?:$ow;
        $this->innerLength = $il?:$ol;
        $this->innerDepth = $id?:$od;
        $this->innerVolume = $iv;
        $this->emptyWeight = $ew ?: 0;
        $this->maxWeight = $mw?: $this->maxWeight;
    }

    public function getReference(): string
    {
        return $this->ref;
    }

    public function getOuterWidth(): int
    {
        return $this->outerWidth;
    }

    public function getOuterLength(): int
    {
        return $this->outerLength;
    }

    public function getOuterDepth(): int
    {
        return $this->outerDepth;
    }

    public function getEmptyWeight(): int
    {
        return $this->emptyWeight;
    }

    public function getInnerWidth(): int
    {
        return $this->innerWidth;
    }

    public function getInnerLength(): int
    {
        return $this->innerLength;
    }

    public function getInnerDepth(): int
    {
        return $this->innerDepth;
    }

    public function getInnerVolume(): int
    {
        return $this->innerVolume ?: round($this->innerWidth*$this->innerLength*$this->innerDepth/pow(10, 9), 5);
    }

    public function getMaxWeight(): int
    {
        return $this->maxWeight;
    }
}

