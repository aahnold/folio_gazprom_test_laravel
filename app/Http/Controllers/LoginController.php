<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function login(Request $request){
        $config = app('config')->get('api');
        $curl = curl_init($config['backend_url'] . $config['auth_uri']);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, [
            'login' => $config['auth_login'],
            'password' => $config['auth_password']
        ]);
        $response = html_entity_decode(curl_exec($curl));
        $xml = new \SimpleXMLElement($response);

        if(!(string) $xml->Response)
            return \Response::json(['status' => 'error', 'message' => 'Authorization unsuccessful.'], 403);
        else {
            if(auth()->attempt([
                'name' => $request->login,
                'password' => $request->password
            ])){
                $user = auth()->user();
                $user->refreshToken();
                $token = $user->api_token;
                return \Response::json(['status' => 'ok', 'token' => $token, 'message' => 'Authorization successful.'], 200);
            }
            else
                return \Response::json(['status' => 'error', 'message' => 'Authorization unsuccessful.'], 403);
        }
    }
}
