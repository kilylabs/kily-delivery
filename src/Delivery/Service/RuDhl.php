<?php

namespace Kily\Delivery\Service;

class RuDhl extends Service
{
    public function getName()
    {
        return 'ru_dhl';
    }

    public function subservices() {
        return [
            'express',
            'international',
        ];
    }
}
