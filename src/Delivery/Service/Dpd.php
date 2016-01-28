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
            'consumer',
            'classic',
            'warehoue',
            'courier',
        ];
    }
}
