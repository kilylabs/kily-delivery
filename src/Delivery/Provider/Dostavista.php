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

use Dostavista\OrderRequest;
use Dostavista\Point;

class Dostavista extends HttpProvider implements ProviderInterface,CalculatorInterface
{
    public function getName()
    {
        return 'dostavista';
    }

    protected function serviceMap()
    {
        return array(
            OrderRequest::DELIVERY_TYPE_CAR=>'dostavista[car]',
            OrderRequest::DELIVERY_TYPE_TRUCK=>'dostavista[truck]',
            OrderRequest::DELIVERY_TYPE_FOOT=>'dostavista[foot]',
        );
    }

    public function supports()
    {
        return [
            'dostavista',
        ];
    }

    public function options() 
    {
        return array_merge(parent::options(),[
            'insurance_sum',
            'test',
        ]);
    }

    protected function getAuthParams()
    {
        return [
            'client_id' => $this->api_id,
            'token' => $this->api_key,
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

        if (isset($opts->test)) {
            if (is_numeric($opts->test)) {
                $tempopts['test'] = (boolean)$opts->test;
            } else {
                throw new BadOption('test');
            }
        }

        parent::setOptions($val, (array) $tempopts);
    }

    public function calculateInternal(Address $to=null)
    {
        if(!$to) {
            throw new RequestError("You really should define destination address to calculate delivery using Dostavista");
        }

        $client = new \Dostavista\Dostavista(new \GuzzleHttp\Client, [
            'baseUrl' => 'https://robotapitest.dostavista.ru/bapi',
            'clientId' => $this->api_id,
            'token' => $this->api_key
        ]);

        $adr = $to->getFullAddress();

            $ret = Service::parseServicesStr($this->serviceMap()[$tarif->id->__toString()]);

        $orderRequest = (new OrderRequest($this->getOption('matter')))
            ->setRequireCar(OrderRequest::DELIVERY_TYPE_FOOT)
            ->setBackpaymentMethod(OrderRequest::BACKPAYMENT_CARD)
            ->setBackpaymentDetails('Карта Сбербанка XXXX, получатель СЕРГЕЙ ИВАНОВИЧ П')
            ->setPoints([
                (new Point(
                    'Москва, Магистральный пер., 1',
                    new DateTime('17:00'),
                    new DateTime('18:00'),
                    '4951234567'
                )),
                (new Point(
                    'Москва, Бобруйская, 28',
                    new DateTime('18:00'),
                    new DateTime('19:00'),
                    '9261234567'
                ))
                ->setTaking(3000),
                ]);

        $deliveryFee = $client->calculateOrder($orderRequest);
        foreach($addresses as $adr) {
            $params = array_merge(
                $this->getAuthParams(),
                [
                    'to_city' => $adr,
                    'weight' => $this->getOption('weight'),
                    'strah' => $this->getOption('insurance_sum', 0),
                    'wd' => $this->getOption('width'),
                    'hg' => $this->getOption('height'),
                    'ln' => $this->getOption('length'),
                ]
            );

            if($zip = $to->getPostalCode()) {
                $params['zip'] = $zip;
            }

            $res = $this->client->post(self::URL, [
                'form_params' => $params,
            ]);

            if ('200' != $res->getStatusCode()) {
                throw new RequestError('Bad status code: '.$res->getStatusCode());
            }

            $xml = $res->getBody()->__toString();
            if (!$xml) {
                throw new RequestError('Got empty response');
            }

            $xml = simplexml_load_string($xml);
            try {
                $this->checkStatusCode($xml->stat->__toString());
            } catch(RequestError $e) {
                continue;
            }
            break;
        }

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
