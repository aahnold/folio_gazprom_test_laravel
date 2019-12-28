<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CardController extends Controller
{
    const CARDS_PER_PAGE = 10;

    public function getCards($id_bill, Request $request){
        $config = app('config')->get('api');
        $curl = curl_init($config['backend_url'] . $config['cards_uri'] . "?id_bill=$id_bill");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $response = html_entity_decode(curl_exec($curl));
        preg_match('/<Card>.*<\/Card>/', $response, $matches);
        if($matches[0]){
            $xml = new \SimpleXMLElement("<?xml version=\"1.0\"?>\n<data>" . $matches[0] . "</data>");
            $data = xml2array($xml);
            if($request->filled('page'))
                $page = $request->page;
            else
                $page = 1;
            if($request->filled('search'))
                $data['Card'] = array_filter($data['Card'], function($card) use ($request) {
                    return mb_strpos((string) $card->description, $request->search) !== false;
                });
            $data['Card'] = array_slice($data['Card'], ($page - 1) * self::CARDS_PER_PAGE, self::CARDS_PER_PAGE);
            return $data;
        }
        else {
            return ['status' => 'error', 'message' => 'Failed to retrieve data.'];
        }
    }

    public function getCard($id_card, Request $request){
        $config = app('config')->get('api');
        $curl = curl_init($config['backend_url'] . $config['card_detail_uri'] . "?id_card=$id_card");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $response = html_entity_decode(curl_exec($curl));
        $xml = new \SimpleXMLElement($response);
        if((int) $xml->errorcode === 0)
            return xml2array($xml->Response);
        else
            return ['status' => 'error', 'message' => (string) $xml->errormessage];
    }
}
