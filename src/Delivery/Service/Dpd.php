<?php

namespace Kily\Delivery\Service;

class Dpd extends Service
{
    public function getName()
    {
        return 'dpd';
    }

    public function subservices() {
        return [
            'classic_courier',
            'express_courier',
            'classic_pvz',
            'express_pvz',
        ];
    }
}
