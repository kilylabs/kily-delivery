<?php

namespace Kily\Delivery\Service;

class Ratek extends Service
{
    public function getName()
    {
        return 'ratek';
    }

    public function subservices() {
        return [
            'warehouse',
            'courier',
        ];
    }
}
