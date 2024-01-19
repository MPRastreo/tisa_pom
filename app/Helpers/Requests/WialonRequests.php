<?php

namespace App\Helpers\Requests;

use App\Services\LogService;
use Exception;
use GuzzleHttp\Client;

class WialonRequests
{
    public static function login() : array | Exception
    {
        try
        {
            $token = LogService::getToken();

            if (empty($token))
            {
                LogService::renewToken();
                return self::login();
            }

            $client = new Client();
            $response = $client->request('GET', 'https://hst-api.wialon.com/wialon/ajax.html?svc=token/login&sid=',
            [
                'form_params' =>
                [
                    'params' =>  '{"token":"'.$token.'","operateAs":"","appName":"","checkService":""}',
                    'sid'   =>  ''
                ]
            ]);

            $sessionWialon = json_decode($response->getBody()->getContents());

            if(isset($sessionWialon->error))
            {
                if (strcmp(strval($sessionWialon->reason), "INVALID_AUTH_TOKEN") === 0)
                {
                    LogService::renewToken();
                    return self::login();
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
            set_time_limit(120000);

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
            set_time_limit(120000);

            $client = new Client();

            $location = $client->request('POST', 'https://geocode-maps.wialon.com/hst-api.wialon.com/gis_geocode?coords=[{"lon":'.$longitud.',"lat":'.$latitud.'}]&flags=1255211008&uid='.$_UID);

            return json_decode($location->getBody()->getContents());
        }
        catch (Exception $ex)
        {
            throw new Exception($ex->getMessage());
        }
    }

    public static function calculateParams(int $n1, int $n2) : int
    {
        return $n1 * $n2;
    }
}
