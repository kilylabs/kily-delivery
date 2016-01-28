<?php

namespace Kily\Delivery\Service;

class Autotrading extends Service
{
    public function getName()
    {
        return 'autotrading';
    }

    public function subservices() {
        return [
            'warehouse',
            'courier',
        ];
    }
}
