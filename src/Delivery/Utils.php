<?php

namespace Kily\Delivery;

use Kily\Delivery\Exception\AddressNotFound;
use Kily\Delivery\Exception\RequestError;

class Utils
{
    public static function camelize($scored)
    {
        return lcfirst(
            implode(
                '',
                array_map(
                    'ucfirst',
                    array_map(
                        'strtolower',
                        explode(
                            '_', $scored)))));
    }

    public static function addrFromString($str)
    {
        $geocoder = Config::get('geocode.provider');

        try {
            $geocode = $geocoder->geocode($str);
            foreach ($geocode as $addr) {
                return $addr;
            }
        } catch (\Ivory\HttpAdapter\HttpAdapterException $e) {
            throw new RequestError($e->__toString());
        }
        throw new AddressNotFound('Unable to geocode string: '.$str);
    }
}
