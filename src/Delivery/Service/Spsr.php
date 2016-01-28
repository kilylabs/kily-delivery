<?php

namespace Kily\Delivery\Service;

class Spsr extends Service
{
    public function getName()
    {
        return 'spsr';
    }

    public function subservices() {
        return [
            'express',
            'international',
        ];
    }
}
