<?php

namespace Kily\Delivery\Service;

class Ponyexpress extends Service
{
    public function getName()
    {
        return 'ponyexpress';
    }

    public function subservices() {
        return [
            'local',
            'international',
        ];
    }
}
