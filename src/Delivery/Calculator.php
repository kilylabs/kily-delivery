<?php

namespace Kily\Delivery;

class Calculator extends BaseDelivery
{

    public function calculate($to = null, $service_codes = null, $options = [])
    {
        return $this->provider->calculate($this->from, $to ?: $this->to, $options ?: $this->options, $service_codes ? $this->getServices($service_codes) : $this->services);
    }

    public function best($to = null, $service_codes = null, $options = [])
    {
        $ret = $this->provider->calculate($this->from, $to ?: $this->to, $options ?: $this->options, $service_codes ? $this->getServices($service_codes) : $this->services);
        $min_delivery = $min_delivery_cost = null;
        foreach ($ret as $idx => $result) {
            if (($min_delivery === null) || ($min_delivery_cost > $result->cost)) {
                $min_delivery = $idx;
                $min_delivery_cost = $result->cost;
            }
        }
        if ($min_delivery === null) {
            return;
        }

        return $ret[$min_delivery];
    }

}
