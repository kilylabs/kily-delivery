<?php

namespace Kily\Delivery\Provider;

use Kily\Delivery\Model\Address;

interface DeliveryInterface 
{

    public function putOrder($order_id=null,Address $to=null, $data=array());
    public function updateOrder($order_id,$data=array());
    public function removeOrder($order_id,$data=array());

}
