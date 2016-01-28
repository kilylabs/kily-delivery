<?php

namespace Kily\Delivery\Service;

class Zheldorexp extends Service
{
    public function getName()
    {
        return 'zheldorexp';
    }

    public function subservices() {
        return [
            'warehouse',
            'courier',
        ];
    }
}
