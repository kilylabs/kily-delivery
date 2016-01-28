<?php

namespace Kily\Delivery\Provider;

interface ProviderInterface
{
    public function getName();
    public function supports();
    public function options();
}
