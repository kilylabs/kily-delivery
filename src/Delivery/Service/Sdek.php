<?php

namespace Kily\Delivery\Service;

class Sdek extends Service
{
    public function getName()
    {
        return 'sdek';
    }

    public function subservices() {
        return [
            'express',
            'superexpress',
            'warehouse',
            'international',
        ];
    }
}
