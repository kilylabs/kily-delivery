<?php

namespace Kily\Delivery\Service;

class RuUps extends Service
{
    public function getName()
    {
        return 'ru_ups';
    }

    public function subservices() {
        return [
            'express',
            'international',
        ];
    }
}
