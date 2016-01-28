<?php

namespace Kily\Delivery\Service;

class Megapolis extends Service
{
    public function getName()
    {
        return 'megapolis';
    }

    public function subservices() {
        return [
            'courier',
            'warehouse',
        ];
    }
}
