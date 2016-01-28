<?php

namespace Kily\Delivery\Service;

class Garantpost extends Service
{
    public function getName()
    {
        return 'garantpost';
    }

    public function subservices() {
        return [
            'local',
            'international',
        ];
    }
}
