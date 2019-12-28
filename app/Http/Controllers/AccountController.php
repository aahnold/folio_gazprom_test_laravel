<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function getBills($login, Request $request){
        $config = app('config')->get('api');
        $curl = curl_init($config['backend_url'] . $config['bills_uri'] . "?login=$login");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $response = html_entity_decode(curl_exec($curl));
        $xml = new \SimpleXMLElement($response);
        if((int) $xml->errorcode === 0)
            return xml2array($xml->Response);
        else
            return ['status' => 'error', 'message' => (string) $xml->errormessage];
    }
}
