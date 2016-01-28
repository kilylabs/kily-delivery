<?php

namespace Kily\Delivery;

use Kily\Delivery\Base\Component;

class CalculatorResponse extends Component
{
    public $service;
    public $cost;
    public $period;

    public function getAttributes()
    {
        return array(
            'service' => $this->service,
            'cost' => $this->cost,
            'period' => $this->period,
        );
    }
}
