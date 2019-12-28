<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DictionaryController extends Controller
{
    public function getDictionary($name){
        $config = app('config')->get('api');
        $curl = curl_init($config['backend_url'] . $config['dictionary_uri'] . "?name=$name");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $response = html_entity_decode(curl_exec($curl));
        $xml = new \SimpleXMLElement($response);
        if((int) $xml->errorcode === 0)
            return xml2array($xml->Response);
        else
            return ['status' => 'error', 'message' => (string) $xml->errormessage];
    }
}
