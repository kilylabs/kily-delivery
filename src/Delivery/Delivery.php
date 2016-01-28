<?php

namespace Kily\Delivery;

use Kily\Delivery\Exception\Exception;

class Delivery extends BaseDelivery
{

    public function putOrder($order_id=null,$data=array())
    {
        if(count($this->provider->services) > 1) {
            throw new Exception('We can delivery only by 1 service at once. There are multiple services defined.');
        }
        return $this->provider->putOrder($order_id,$this->to,$this->provider->parseOptions($data,$this->provider->options));
    }

    public function updateOrder($order_id=null,$data=array())
    {
        return $this->provider->updateOrder($order_id,$data);
    }

    public function removeOrder($order_id=null,$data=array())
    {
        return $this->provider->removeOrder($order_id,$data);
    }

}
