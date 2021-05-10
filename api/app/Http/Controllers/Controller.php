<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function array2xml($array, $xml = false){
        if($xml === false){
            $xml = new \SimpleXMLElement('<result/>');
        }

        foreach($array as $key => $value){
            if(is_array($value)){
                $this->array2xml($value, $xml->addChild($key));
            } else {
                $xml->addChild($key, $value);
            }
        }

        return $xml->asXML();
    }

    public function responseWithType($data, $type) {
        $response = response($data)->header('Content-Type', $type);

        if (strpos($type, 'json') !== false) {
            return $response;
        } elseif (strpos($type, 'xml') !== false) {
            $arr = json_decode($response->content(), true);
            return $this->array2xml($arr, false);
        }
    }

    public function getRequestType($request) {
        $type = 'application/json';
        $ct = $request->header('Content-Type');
        $a = $request->header('Accept');

        if ($ct || $a) {
            if (strpos($ct, 'json') || strpos($ct, 'xml')) {
                $type = $ct;
            } elseif (strpos($a, 'json') || strpos($a, 'xml')) {
                $type = $a;
            }
        }
        
        return $type;
    }
}
