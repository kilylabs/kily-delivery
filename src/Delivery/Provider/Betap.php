<?php

namespace Kily\Delivery\Provider;

use Kily\Delivery\Exception\Exception;
use Kily\Delivery\Exception\RequestError;
use Kily\Delivery\Exception\BadOption;
use Kily\Delivery\Model\Address;
use Kily\Delivery\CalculatorResponse;
use Kily\Delivery\Service\Service;
use Kily\Delivery\Service\ServiceFactory;
use Kily\Delivery\Config;

class Betap extends HttpProvider implements ProviderInterface,CalculatorInterface
{
    const URL = 'https://fb.dm-sg.ru:8080/wsrv';
    const REQUEST_TYPE = 54; 
    const VERSION = 1;

    public function getName()
    {
        return 'betap';
    }

    protected function serviceMap()
    {
        return array(
            'RussianPost-0-4' => 'russianpost[parcel]',
            'RussianPost-0-3' => 'russianpost[bookpost]',
            'RussianPost-0-16' => 'russianpost[firstclass]',
            'DPD-0-CSM'=>'dpd[express_courier]',
            'DPD-0-PCL'=>'dpd[classic_courier]',
            'DPD-1-CSM'=>'dpd[express_pvz]',
            'DPD-1-PCL'=>'dpd[classic_pvz]',
            'DPD-1-PCL'=>'dpd[classic_pvz]',
            'CDEK-1-136'=>'sdek[warehouse]',
            'CDEK-0-137'=>'sdek[express]',
        );
    }

    public function supports()
    {
        return [
            'russianpost',
            'spsr',
            'sdek',
            'dpd',
            'boxberry',
            'pickpoint',
            'maxipost',
            'betapost',
        ];
    }

    public function options() 
    {
        return array_merge(parent::options(),[
            'cod_sum',
            'value_sum'
        ]);
    }

    protected function getAuthParams()
    {
        return [
            'id' => $this->api_id,
            'p' => $this->api_key,
        ];
    }

    public function setOptions($val, $prevopts = [])
    {
        $tempopts = array();
        $opts = (object) $val;

        if (isset($opts->cod_sum)) {
            if (is_numeric($opts->cod_sum)) {
                $tempopts['cod_sum'] = $opts->cod_sum;
            /*} else {
                throw new BadOption('cod_sum');
            */
            }
        }

        parent::setOptions($val, (array) $tempopts);
    }

    public function calculateInternal(Address $to=null)
    {
        $xml = new \SimpleXMLElement("<request></request>");
        $xml->addAttribute('partner_id', $this->api_id);
        $xml->addAttribute('password', $this->api_key);
        $xml->addAttribute('request_type',self::REQUEST_TYPE);

        $xparcel = $xml->addChild('parcel');

        if($to) {
            if(!$this->getOption('use_structed_address')) {
                if($v = $to->getFullAddress()) {
                    $xparcel->addAttribute('addr',$v);
                }
            } else {
                if($v = $to->getPostalCode()) {
                    $xparcel->addAttribute('zip', $v);
                }
                if($v = $to->getLocality()) {
                    $xparcel->addAttribute('city', $v);
                }
                if($v = $to->getStreetName()) {
                    $xparcel->addAttribute('street', $v);
                }
                if($v = $to->getStreetNumber()) {
                    $xparcel->addAttribute('house', $v);
                }
            }
        }

        if($v = $this->getOption('value_sum')) {
            $xparcel->addAttribute('sum_vl', $v);
        }
        if($v = $this->getOption('cod_sum')) {
            $xparcel->addAttribute('sum_nalog', $v);
        }
        if($v = $this->getOption('weight')) {
            $xparcel->addAttribute('weight', $v);
        }
        if($v = $this->getOption('volume')) {
            $xparcel->addAttribute('volume', $v);
        } else {
            if($v = $this->getOption('dimensions')) {
                $volume = array_product(explode('x',$v));
                if($volume) {
                    $volume = round($volume/1000000,4);
                    $xparcel->addAttribute('volume', $volume);
                }
            }
        }
        $xparcel->addAttribute('version',self::VERSION);

        //echo $xml->asXML();

        $res = $this->client->post(self::URL, [
            'verify'=>false,
            'body' => $xml->asXML(),
        ]);

        if ('200' != $res->getStatusCode()) {
            throw new RequestError('Bad status code: '.$res->getStatusCode());
        }

        $xml = $res->getBody()->__toString();
        //echo $xml;
        if (!$xml) {
            throw new RequestError('Got empty response');
        }

        $xml = $res->xml();
        $this->checkStatusCode($xml[0]['state'],$xml);

        $all = [];
        $services = $this->services;
        foreach ($xml->parcel[0]->sd as $tarif) {
            $tarif_id = implode('-',[$tarif[0]['sd_name'],$tarif[0]['pvz'],$tarif[0]['tariff_code']]);
            if (!isset($this->serviceMap()[$tarif_id])) {
                continue;
            }

            $ret = Service::parseServicesStr($this->serviceMap()[$tarif_id]);
            if(!$ret) {
                throw new Exception("There is a error in serviceMap: ".print_r($this->serviceMap()[$tarif_id],true));
            }

            list($service,$subservices) = $ret[0];

            $service = ServiceFactory::factory($service,$subservices);
            $cr = new CalculatorResponse();
            $cr->service = $service;
            $cr->cost = isset($tarif['tariff']) ? $tarif['tariff'] : $tarif->price->__toString();
            $cr->period = isset($tarif['days']) ? $tarif['days'] : $tarif->dat->__toString();

            if($services) {
                foreach($services as $c_service) {
                    if($c_service->equalsTo($service)) {
                        $all[] = $cr;
                        break;
                    }
                } 
            } else {
                $all[] = $cr;
            }

        }

        return $all;
    }

    protected function checkStatusCode($status,$resp=null)
    {
        switch ($status) {
        case '0': return true;
        case '-1': throw new RequestError('запрос не выполнен: '.$resp->error->code->__toString().' - '.$resp->error->msg->__toString());
        case '-2': throw new RequestError('запрос поставлен в очередь');
        default: throw new RequestError('Unknown status code: '.$status);
        }
    }
}
