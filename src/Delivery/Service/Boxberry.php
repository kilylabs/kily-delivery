<?php

namespace Kily\Delivery\Service;

class Boxberry extends Service
{
    public function getName()
    {
        return 'boxberry';
    }

    public function subservices() {
        return [
            'warehouse',
            'courier',
        ];
    }
}
