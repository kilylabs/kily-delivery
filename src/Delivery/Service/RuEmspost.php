<?php

namespace Kily\Delivery\Service;

class RuEmspost extends Service
{
    public function getName()
    {
        return 'ru_emspost';
    }

    public function subservices() {
        return [
            'local',
            'international',
        ];
    }
}
