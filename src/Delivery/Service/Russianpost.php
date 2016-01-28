<?php

namespace Kily\Delivery\Service;

class Russianpost extends Service
{
    public function getName()
    {
        return 'russianpost';
    }

    public function subservices() {
        return [
            'parcel',
            'bookpost',
            'firstclass',
        ];
    }
}
