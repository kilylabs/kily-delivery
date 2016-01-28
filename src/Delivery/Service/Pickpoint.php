<?php

namespace Kily\Delivery\Service;

class Pickpoint extends Service
{
    public function getName()
    {
        return 'spsr';
    }

    public function subservices() {
        return [
        ];
    }
}
