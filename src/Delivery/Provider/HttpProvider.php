<?php

namespace Kily\Delivery\Provider;

use Kily\Delivery\Config;

class HttpProvider extends Provider
{
    protected $_api_id;
    protected $_api_key;

    protected $_client;

    public function __construct($api_id=null,$api_key=null) {

        $this->api_id = $api_id;
        $this->api_key = $api_key;

        $this->client = new \GuzzleHttp\Client([
            'debug' => Config::get('debug'),
        ]);

        parent::__construct();
    }

    public function getApi_id() {
        return $this->_api_id;
    }
    public function setApi_id($val) {
        $this->_api_id = $val;
        return $this;
    }

    public function getApi_key() {
        return $this->_api_key;
    }
    public function setApi_key($val) {
        $this->_api_key = $val;
        return $this;
    }

    public function setClient($val) {
        $this->_client = $val;
        return $this;
    }

    public function getClient() {
        return $this->_client;
    }
}
