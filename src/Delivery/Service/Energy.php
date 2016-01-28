<?php

namespace Kily\Delivery\Service;

class Energy extends Service
{
    public function getName()
    {
        return 'energy';
    }

    public function subservices() {
        return [
            'auto_warehouse',
            'railway_warehouse',
            'avia_warehouse',
            'ship_warehouse',
            'auto_courier',
            'railway_courier',
            'avia_courier',
            'ship_courier',
        ];
    }
}
