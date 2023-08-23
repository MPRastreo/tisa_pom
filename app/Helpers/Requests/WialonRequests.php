<?php

namespace App\Helpers\Requests;

use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Arr;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Inspector\InspectorInterface;

class WialonRequests
{
    public static function loginWialon() : array | Exception
    {
        try
        {
            $client = new Client();
            $response = $client->request('GET', 'https://hst-api.wialon.com/wialon/ajax.html?svc=token/login&sid=',
            [
                'form_params' =>
                [
                    'params' =>  '{"token":"9029f92f51ddd2d6f9b477235e32029fA551355ADABF65A4D32A684656C663CF57B266D8","operateAs":"","appName":"","checkService":""}',
                    'sid'   =>  ''
                ]
            ]);

            $sessionWialon = json_decode($response->getBody()->getContents());

            if(isset($sessionWialon->error))
            {
                if (strcmp(strval($sessionWialon->reason), "INVALID_AUTH_TOKEN") === 0)
                {
                    throw new Exception('Los accesos han cambiado, favor de contactar al proveedor');
                }
            }

            return ["_ID" => $sessionWialon->eid, "_UID" => $sessionWialon->user->id];
        }
        catch (Exception $ex)
        {
            throw new Exception($ex->getMessage());
        }
    }

    public static function getDataUnits($_ID)
    {
        try
        {

            $client = new Client();
            $response = $client->request('GET', 'https://hst-api.wialon.com/wialon/ajax.html?svc=core/update_data_flags&sid=' . $_ID,
            [
                'form_params' =>
                [
                    'params' =>  '{"spec":[{"type":"type","data":"avl_unit","flags":4611686018427387903,"mode":0}]}',
                    'sid'   =>  $_ID
                ]
            ]);

            return json_decode($response->getBody()->getContents());
        }
        catch (Exception $ex)
        {
            throw new Exception($ex->getMessage());
        }
    }

    public static function getLocation($_UID, $latitud, $longitud)
    {
        try
        {
            set_time_limit(120);
            $client = new Client();
            // $location = $client->request('POST', 'https://geocode-maps.wialon.com/hst-api.wialon.com/gis_geocode?flags=0&city_radius=0&dist_from_unit=0&txt_dist=&house_detect_radius=0&coords=%5B%7B%22lat%22%3A'.$latitud.'%2C%22lon%22%3A'.$longitud.'%7D%5D&uid=26144132&sid='.$_ID);

            $location = $client->request('POST', 'https://geocode-maps.wialon.com/hst-api.wialon.com/gis_geocode?coords=[{"lon":'.$longitud.',"lat":'.$latitud.'}]&flags=1255211008&uid='.$_UID);
            return json_decode($location->getBody()->getContents());
        }
        catch (Exception $ex)
        {
            throw new Exception($ex->getMessage());
        }
    }

    /**
     * Calculate params between two numbers.
     *
     * Request options to apply here, the Takis are better than Doritos
     * Change, my mind.
     *
     * @param int $n1 Number one.
     * @param int $n2 Number two.
     *
     * @return int
     */
    public static function calculateParams(int $n1, int $n2) : int
    {
        return $n1 * $n2;
    }
}
