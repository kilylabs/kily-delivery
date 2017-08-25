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

class Edost extends HttpProvider implements ProviderInterface,CalculatorInterface
{
    const URL = 'http://www.edost.ru/edost_calc_kln.php';

    public function getName()
    {
        return 'edost';
    }

    protected function serviceMap()
    {
        return array(
            '1'=>'russianpost[firstclass]',
            '2'=>'russianpost[parcel,bookpost]',
            '3'=>'ru_emspost[local]',
            '5'=>'spsr[express]',
            '6'=>'sdek[express]',
            '9'=>'sdek[warehouse]',
            '10'=>'sdek[superexpress]',
            '11'=>'ru_dhl[express]',
            '12'=>'ru_ups[exprexx]',
            '14'=>'zheldorexp[warehouse]',
            '15'=>'autotrading[warehouse]',
            '16'=>'pek[warehouse]',
            '17'=>'sdek[international]',
            '18'=>'ru_emspost[international]',
            '19'=>'spsr[international]',
            '20'=>'ru_dhl[international]',
            '21'=>'ru_ups[international]',
            '22'=>'dellin[warehouse]',
            '23'=>'megapolis[courier]',
            '24'=>'megapolis[terminal]',
            '25'=>'garantpost[local]',
            '26'=>'garantpost[international]',
            '27'=>'ponyexpress[local]',
            '28'=>'ponyexpress[international]',
            '29'=>'pickpoint',
            '36'=>'boxberry[warehouse]',
            '37'=>'sdek[warehouse]',
            '38'=>'sdek[express]',
            '39'=>'energy[auto_warehouse]',
            '40'=>'energy[railway_warehouse]',
            '41'=>'energy[avia_warehouse]',
            '42'=>'energy[ship_warehouse]',
            '43'=>'boxberry[courier]',
            '44'=>'dpd[classic]',
            '45'=>'dpd[consumer]',
            '46'=>'dpd[warehouse]',
            '47'=>'dpd[courier]',
            '48'=>'zheldorexp[courier]',
            '49'=>'pek[courier]',
            '50'=>'autotrading[courier]',
            '51'=>'dellin[courier]',
            '52'=>'energy[auto_courier]',
            '53'=>'energy[railway_courier]',
            '54'=>'energy[avia_courier]',
            '55'=>'energy[ship_courier]',
            '59'=>'ratek[warehouse]',
            '60'=>'ratek[courier]',
        );
    }

    public function supports()
    {
        return [
            'autotrading',
            'boxberry',
            'dellin',
            'dpd',
            'energy',
            'garantpost',
            'megapolis',
            'pek',
            'pickpoint',
            'ponyexpress',
            'ratek',
            'ru_dhl',
            'ru_emspost',
            'russianpost',
            'ru_ups',
            'sdek',
            'spsr',
            'zheldorexp',
        ];
    }

    public function options() 
    {
        return array_merge(parent::options(),[
            'insurance_sum',
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

        if (isset($opts->insurance_sum)) {
            if (is_numeric($opts->insurance_sum)) {
                $tempopts['insurance_sum'] = $opts->insurance_sum;
            } else {
                throw new BadOption('insurance_sum');
            }
        }

        parent::setOptions($val, (array) $tempopts);
    }

    public function calculateInternal(Address $to=null)
    {
        if(!$to) {
            throw new RequestError("You really should define destination address to calculate delivery using edost.ru");
        }
        if(!$to->getLocality()) {
            throw new RequestError("Edost requires city (getLocality()) to be defined. The current address string: ".$to->getFullAddress());
        }
        $params = array_merge(
            $this->getAuthParams(),
            [
                'to_city' => $to->getLocality(),
                'weight' => $this->getOption('weight'),
                'strah' => $this->getOption('insurance_sum', 0),
                'wd' => $this->getOption('width'),
                'hg' => $this->getOption('height'),
                'ln' => $this->getOption('length'),
            ]
        );
        $res = $this->client->post(self::URL, [
            'body' => $params,
        ]);

        if ('200' != $res->getStatusCode()) {
            throw new RequestError('Bad status code: '.$res->getStatusCode());
        }

        $xml = $res->getBody()->__toString();
        if (!$xml) {
            throw new RequestError('Got empty response');
        }

        $xml = $res->xml();
        $this->checkStatusCode($xml->stat->__toString());

        $all = [];

        $services = $this->services;

        foreach ($xml->tarif as $tarif) {
            if (!isset($this->serviceMap()[$tarif->id->__toString()])) {
                continue;
            }

            $ret = Service::parseServicesStr($this->serviceMap()[$tarif->id->__toString()]);
            if(!$ret) {
                throw new Exception("There is a error in serviceMap: ".print_r($this->serviceMap()[$tarif->id->__toString()],true));
            }

            list($service,$subservices) = $ret[0];

            $service = ServiceFactory::factory($service,$subservices);
            $cr = new CalculatorResponse();
            $cr->service = $service;
            $cr->cost = $tarif->price->__toString();
            $cr->period = $tarif->dat->__toString();

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

    protected function checkStatusCode($status)
    {
        switch ($status) {
        case '1': return true;
        case '2': throw new RequestError('доступ к расчету заблокирован');
        case '3': throw new RequestError('неверные данные магазина (пароль или идентификатор)');
        case '4': throw new RequestError('неверные входные параметры');
        case '5': throw new RequestError('неверный город или страна');
        case '6': throw new RequestError('внутренняя ошибка сервера расчетов');
        case '7': throw new RequestError('не заданы компании доставки в настройках магазина');
        case '8': throw new RequestError('сервер расчета не отвечает');
        case '9': throw new RequestError('превышен лимит расчетов за день');
        case '11': throw new RequestError('не указан вес');
        case '12': throw new RequestError('не заданы данные магазина (пароль или идентификатор)');
        default: throw new RequestError('Unknown status code: '.$status);
        }
    }
}
