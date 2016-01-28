<?php

namespace Kily\Delivery\Service;

class Dellin extends Service
{
    public function getName()
    {
        return 'dellin';
    }

    public function subservices() {
        return [
            'warehouse',
            'courier',
        ];
    }
}
