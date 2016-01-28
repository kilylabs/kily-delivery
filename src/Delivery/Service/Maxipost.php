<?php

namespace Kily\Delivery\Service;

class Maxipost extends Service
{
    public function getName()
    {
        return 'maxipost';
    }

    public function subservices() {
        return [
            'warehouse',
            'courier',
        ];
    }
}
