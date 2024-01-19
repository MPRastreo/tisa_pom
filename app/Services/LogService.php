<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LogService
{
    private const logUrl = "https://log.wsmprastreo.com.mx/api/v1/";
    private const accessToken = "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiI5YWJkMWY5My05MGM1LTQzYjQtODc2YS1lMWNjN2U5MTU5YzciLCJqdGkiOiI2YjA2MmIwMzEzMjFhMDU1ZTY3NDViYWEwNmJmOTM5MDE5MTYxN2NmYTZhYTUzZDdlOWE0MDk0ODI2Y2ZhYmRhYmJlMjE0MTIxMTVjNjE1ZCIsImlhdCI6MTcwMTM3MTI1MC43MjA4MDQ5Mjk3MzMyNzYzNjcxODc1LCJuYmYiOjE3MDEzNzEyNTAuNzIwODA4OTgyODQ5MTIxMDkzNzUsImV4cCI6MTczMjk5MzY1MC43MTQ2MjIwMjA3MjE0MzU1NDY4NzUsInN1YiI6IjEiLCJzY29wZXMiOltdfQ.joP765HArEGK2mLMIm1CSnx_yJBxWR_btGd_XW5gtd6Naxno8-vgYyD8taYCX7fCl6OB_VrYmxt4ZAbtNGZtWnvb8pNAvvFk8pf1bDYhGudsGZN5NQE04n6ZVahNLlXT_AqWz1VUg6E61riogj1AUFnTCceDDZpMl_6oDkO936Q3P3gKFXrpkIN3YKHYPOk5fGIAJlaZ6kUtV59DtGtW9BSYu3BzlrS90kgSlIVwKef0oF3Uk_GNMy51e88FHbp69Zz1udgeWVy11uCE7fIMP4_17NDsxQKmSqAzKAndVrXtTUmeYnxgQrMvfvrqD5gtndaadAGFKUJc-7z6Ulivao2DNyHE_Ur5IK4vPLN-kytV6e1XLKY5VuahWih-J6Bah0Kfbh3ivfUCKXxhyu3ntepdBVxh_1zVQsxYLATSrXEFcHMA7Fx_iVVNxh8KTkedxPdUdTb2S9yRZ8CDFwZWbufM8kAkyVrr348d_aKHGVG2vrQnHCg8e3MdlJM1A-R_HZZOp5uyT-t9qALe9FSc8daI2yMgQ9hv-0dYbZBGv3vpDgayH_kwLaMHU8XBAl2uX7zdCI6Ce-SJN5N2EkeET8tgBGnxtkVu9uU85ZUC4xQqYC4iAaV2CeOhErwocy1gFm2-gkHsxsnBCfUl9xXf7QwzExZhF_WuQ2-yZ4w_S5c";
    private const projectName = "API ORMU - TISA POM";
    private const customerName = "ORMU";
    private const customerTokenId = 12;

    public static function sendToLog($errorDescription = 'Any') : void
    {
        try
        {
            $body =
            [
                'project_name' => self::projectName,
                'customer_name' => self::customerName,
                'error_description' => $errorDescription,
            ];

            $response = Http::withHeaders
            ([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer '.self::accessToken,
                'Content-Type' => 'application/json'
            ])->post(self::logUrl.'log', $body);

            if ($response->ok())
            {
                Log::info('Error log sent!');
            }
            else if ($response->unauthorized())
            {
                Log::error('Token expired!');
            }
            else
            {
                Log::error('API log bot error :(');
            }
        }
        catch (Exception $ex)
        {
            Log::error($ex->getMessage());
        }
    }

    public static function getToken() : String
    {
        try
        {
            $response = Http::withHeaders
            ([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer '.self::accessToken,
                'Content-Type' => 'application/json'
            ])->get(self::logUrl.'credentials/'.self::customerTokenId);

            if ($response->ok())
            {
                $object = json_decode($response->body());
                return $object->token;
            }
            else if ($response->unauthorized())
            {
                throw new Exception('Token expired');
            }
            else
            {
                throw new Exception('Error when getting token');
            }
        }
        catch (Exception $ex)
        {
            throw new Exception($ex->getMessage());
        }
    }

    public static function renewToken() : String
    {
        try
        {
            $response = Http::withHeaders
            ([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer '.self::accessToken,
                'Content-Type' => 'application/json'
            ])->post(self::logUrl.'credentials/token/renew/'.self::customerTokenId);

            if ($response->ok())
            {
                $object = json_decode($response->body());
                return $object->message;
            }
            else if ($response->unauthorized())
            {
                throw new Exception('Unauthorized when renewing token');
            }
            else
            {
                throw new Exception('Error when renewing token');
            }
        }
        catch (Exception $ex)
        {
            throw new Exception($ex->getMessage());
        }
    }
}
