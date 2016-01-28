<?php

namespace Kily\Delivery\Exception;

class BadOption extends Exception
{
    public function __construct($options=[],$msg='',$code=0,$previous=null) 
    {
        parent::__construct(implode(',',(array)$options)?:$msg,$code,$previous);
    }
}
