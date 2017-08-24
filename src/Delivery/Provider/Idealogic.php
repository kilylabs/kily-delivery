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

class Idealogic extends HttpProvider implements ProviderInterface, DeliveryInterface, CalculatorInterface
{
    const URL = 'http://www.megaexp.ru/api.php';
    const PUT_DOC_TYPE = 100;

    public function getName()
    {
        return 'idealogic';
    }

    public function supports()
    {
        return [
            'russianpost',
            'ru_emspost',
            'idealogic',
        ];
    }

    public function options() 
    {
        return array_merge(parent::options(),[
            'agent_id',
            'capacity',
            'comment',
            'cost_article',
            'cost_delivery',
            'cost_insurance',
            'cost_public',
            'items',
            'op_category',
            'op_fragile',
            'op_inventory',
            'op_notification',
            'op_packing',
            'op_packing ',
            'op_take',
            'op_type',
            'registry',
            'weight_article',
            'weight_end',
        ]);
    }

    protected function serviceMap()
    {
        return array(
            'RussianPost-0-4' => 'russianpost[parcel]',
            'RussianPost-0-3' => 'russianpost[bookpost]',
            'RussianPost-0-16' => 'russianpost[firstclass]',
        );
    }

    protected function service2Id() 
    {
        return array(
            'ru_emspost'=>1,
            'russianpost'=>2,
            'idealogic'=>4,
        );
    }

    protected function country2Id() {
        return array(
            'Россия'=>643,
            'Казахстан'=>398,
            'Белоруссия'=>112
        );
    }


    public function getAuthParams()
    {
        return [
            'key' => $this->api_id,
            'secret' => $this->api_key,
        ];
    }

    public function setOptions($val, $prevopts = [])
    {
        $tempopts = array();
        $opts = (object) $val;

        if (isset($opts->cod_sum)) {
            if (is_numeric($opts->cod_sum)) {
                $tempopts['cod_sum'] = $opts->cod_sum;
            } else {
                throw new BadOption('cod_sum');
            }
        }

        parent::setOptions($val, (array) $tempopts);
    }

    public function calculateInternal(Address $to = null)
    {
        $xml = new \SimpleXMLElement("<request></request>");
        $xml->addAttribute('partner_id', $this->api_id);
        $xml->addAttribute('password', $this->api_key);
        $xml->addAttribute('request_type',self::REQUEST_TYPE);

        $xparcel = $xml->addChild('parcel');

        if($to) {
            if(!$this->getOption('use_structed_address')) {
                if($v = $to->fullAddress) {
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
        $xparcel->addAttribute('version',self::VERSION);

        $res = $this->client->post(self::URL, [
            'verify'=>false,
            'body' => $xml->asXML(),
        ]);

        if ('200' != $res->getStatusCode()) {
            throw new RequestError('Bad status code: '.$res->getStatusCode());
        }

        $xml = $res->getBody()->__toString();
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

/*
<?xml version="1.0" encoding="UTF-8"?>
<document>
    <doc_type>100</doc_type>
    <key>admin</key>
<doc_date>08.05.2013 11:50:03</doc_date>
    <order>
        <registry>787687</registry>
        <megapolis>m123123</megapolis>
<agent_id>1</agent_id>
        <comment>доставить с 18 до 21 в рабочие дни</comment>
        <articles>
            <article>
                <article_id>f324</article_id>
                <article_name>пижама</article_name>
                <article_price>200.56</article_price>
                <article_weight>0.8</article_weight>
                <article_count>44</article_count>
            </article>
            <article>
                <article_id>f980</article_id>
                <article_name>планшет Samsung 8.0" n5100 16gb белый</article_name>
                <article_price>21990</article_price>
                <article_weight>1.2</article_weight>
                <article_count>1</article_count>
            </article>
            <article>
                <article_id>f944</article_id>
                <article_name>Гарнитура Plantronix m155 bluetooth multipoint</article_name>
                <article_price>1780.99</article_price>
                <article_weight>0.4</article_weight>
                <article_count>9</article_count>
            </article>
        </articles>
        <option>
            <op_inventory>1</op_inventory>
            <op_category>1</op_category>
            <op_type>2</op_type>
            <op_notification></op_notification>
            <op_fragile>0</op_fragile>
            <op_packing>2</op_packing>
            <op_take>1</op_take>
        </option>
        <cost>
            <cost_delivery>38420.03</cost_delivery>
            <cost_insurance>400</cost_insurance>
            <cost_public>38420.03</cost_public>
            <cost_article>38420.03</cost_article>
        </cost>
        <dimension>
            <weight_end>38420.03</weight_end>
            <weight_article>6.4</weight_article>
            <capacity>0.8</capacity>
        </dimension>
        <person>
    <fio>Иванов Иван Иванович</fio>
            <phone>+7(926)344-77-88</phone>
            <email>none@mail.ru</email>
        </person>
        <address>
            <country>643</country>
            <zip>121467</zip>
            <region>Москва</region>
            <area></area>
            <city>Москва</city>
<place></place>
            <street>Ленинградский проспект</street>
            <house>3</house>
            <building>5</building>
    <flat>78</flat>
            <office></office>
            <floor>4</floor>
            <full>г. Москва ленинградский проспект д.3 стр.5 офис 78 этаж 4</full>
        </address>
        <client>
            <client_contract>д9923-23-07-2009</client_contract>
        </client>
    </order>
</document>
*/
    public function removeOrder($order_id=null,$data=[]) {
    }

    public function updateOrder($order_id=null,$data=[]) {
    }

    public function putOrder($order_id=null,Address $to=null, $data=[])
    {
        if(!$order_id) {
            throw new BadOption('order_id');
        }

        if(!$data->agent_id) {
            $service = @reset($this->services);
            if(!$service)
                throw new BadOption('agent_id');

            if(isset($this->service2Id()[$service->getName()])) {
                $data->agent_id = $this->service2Id()[$service->getName()];
            } else {
                throw new BadOption('agent_id');
            }
        }

        $xml = new \XMLWriter();
        $xml->openMemory();
        $xml->startDocument("1.0","UTF-8");
        $xml->startElement('document');
        {
            $this->element($xml,'doc_type',self::PUT_DOC_TYPE);
            $this->element($xml,'key',$this->authParams['key']);
            $this->element($xml,'doc_date',(new \DateTime)->format('d.m.Y H:i:s'));
            $xml->startElement('order');
            {
                $this->element($xml,'registry',$data->registry?:date('Ymd'));
                $this->element($xml,'megapolis',$order_id);
                $this->element($xml,'agent_id',$data->agent_id);
                if($data->comment) {
                    $this->element($xml,'comment',$data->comment);
                }
                if($data->items) {
                    $xml->startElement('articles');
                    foreach($data->items as $item) {
                        $xml->startElement('article');
                        {
                            $this->element($xml,'article_id',$item->sku);
                            $this->element($xml,'article_name',$item->name);
                            $this->element($xml,'article_price',$item->price->getPrice());
                            $this->element($xml,'article_weight',$item->weight->inKg());
                            $this->element($xml,'article_count',$item->count);
                        }
                        $xml->endElement();
                    }
                    $xml->endElement();
                }
                if($data->op_inventory 
                    || $data->op_category 
                    || $data->op_notification 
                    || $data->op_fragile 
                    || $data->op_packing 
                    || $data->op_type
                    || $data->op_take) {
                    $xml->startElement('option');
                        if($data->op_inventory) $this->element($xml,'op_inventory',$data->op_inventory);
                        if($data->op_category) $this->element($xml,'op_category',$data->op_category);
                        if($data->op_notification) $this->element($xml,'op_notification',$data->op_notification);
                        if($data->op_fragile) $this->element($xml,'op_fragile',$data->op_fragile);
                        if($data->op_packing) $this->element($xml,'op_packing',$data->op_packing);
                        if($data->op_type) $this->element($xml,'op_type',$data->op_type);
                        if($data->op_take) $this->element($xml,'op_take',$data->op_take);
                    $xml->endElement();
                }

                $xml->startElement('cost');
                {
                    if($data->cost_delivery) $this->element($xml,'cost_delivery',$data->cost_delivery);
                    if($data->cost_insurance) $this->element($xml,'cost_insurance',$data->cost_insurance);
                    if($data->cost_public) $this->element($xml,'cost_public',$data->cost_public);
                    elseif($data->items) $this->element($xml,'cost_public',$data->items->getTotalCost()->getPrice());
                    if($data->cost_article) $this->element($xml,'cost_article',$data->cost_article);
                    elseif($data->items) $this->element($xml,'cost_article',$data->items->getTotalCost()->getPrice());
                }
                $xml->endElement();
                $xml->startElement('dimension');
                {
                    if($data->weight_end) $this->element($xml,'weight_end',$data->weight_end);
                    if($data->weight_article) $this->element($xml,'weight_article',$data->weight_article);
                    elseif($data->items) $this->element($xml,'weight_article',$data->items->getTotalWeight()->inKg());
                    if($data->capacity) $this->element($xml,'capacity',$data->capacity);
                    elseif($data->items) $this->element($xml,'capacity',$data->items->getTotalVolume()->inM3());
                }
                $xml->endElement();
                $xml->startElement('person');
                {
                    $this->element($xml,'phone',$data->person->phone);
                    if($data->person->email) $this->element($xml,'email',$data->person->email);
                    $this->element($xml,'fio',$data->person->fullName);
                }
                $xml->endElement();
                $xml->startElement('address');
                {
                    if($to instanceof RawAddress) {
                        $this->element($xml,'full',$to->fullAddress);
                    } else {
                        if($to->country && ($country = $to->country->name) && isset($this->country2Id()[$country]) && ($country_id = $this->country2Id()[$country])) {
                            $this->element($xml,'country',$country_id);
                        }
                        if($to->postalCode) {
                            $this->element($xml,'zip',$to->postalCode);
                        }
                        if($to->adminLevels || $to->locality) {
                            $this->element($xml,'region',isset($to->adminLevels[0]['name'])?$to->adminLevels[0]['name']:$to->locality);
                        }
                        if($to->locality) {
                            $this->element($xml,'city',$to->locality);
                        }
                        if($to->streetName) {
                            $this->element($xml,'street',$to->streetName);
                        }
                        if($to->streetNumber) {
                            $this->element($xml,'house',$to->streetNumber);
                        }
                        if($to->flat) {
                            $this->element($xml,'flat',$to->flat);
                        }
                    }
                }
                $xml->endElement();
                $xml->startElement('client');
                {
                    $this->element($xml,'client_contract',$this->authParams['key']);
                }
                $xml->endElement();
            }
            $xml->endElement();
        }
        $xml->endElement();
        echo $xml->outputMemory(true);
        die();

        $doc_type = $this->setXmlVal($xml,$xml->addChild('doc_type'),self::PUT_DOC_TYPE);
        $key = $this->setXmlVal($xml->addChild('key'),$this->authParams['key']);
        $doc_date = $this->setXmlVal($xml->addChild('doc_date'),(new DateTime)->format('d.m.Y H:i:s'));

        $order = $xml->addChild('order');
        $registry = $this->setXmlVal($order->addChild('megapolis'),$order_id);
        $registry = $this->setXmlVal($order->addChild('agent_id'),$this->authParams['key']);

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
        $xparcel->addAttribute('version',self::VERSION);

        echo $xml->asXML();

        $res = $this->client->post(self::URL, [
            'verify'=>false,
            'body' => $xml->asXML(),
        ]);

        if ('200' != $res->getStatusCode()) {
            throw new RequestError('Bad status code: '.$res->getStatusCode());
        }

        $xml = $res->getBody()->__toString();
        echo $xml;
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
            foreach($services as $c_service) {
                if($c_service->equalsTo($service)) {
                    $cr = new CalculatorResponse();
                    $cr->service = $service;
                    $cr->cost = $tarif->price->__toString();
                    $cr->period = $tarif->dat->__toString();

                    $all[] = $cr;
                }
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

    protected function element($xml,$name,$val,$attrs=[]) {
        $xml->startElement($name);
        $xml->text($val);
        $xml->endElement();
    }
}
