<?php

namespace Kily\Delivery\Provider;

use Kily\Delivery\Model\Address;

interface CalculatorInterface 
{

    public function calculate(Address $from = null, Address $to = null, $options = [], $services = []);
    public function calculateInternal(Address $to=null);

}
