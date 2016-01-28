<?php

namespace Kily\Delivery\Service;

class Betapost extends Service
{
    public function getName()
    {
        return 'betapost';
    }

    public function subservices() {
        return [
        ];
    }
}
