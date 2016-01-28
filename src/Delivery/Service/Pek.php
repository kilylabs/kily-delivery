<?php

namespace Kily\Delivery\Service;

class Pek extends Service
{
    public function getName()
    {
        return 'pek';
    }

    public function subservices() {
        return [
            'warehouse',
            'courier',
        ];
    }
}
