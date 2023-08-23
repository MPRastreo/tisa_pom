<?php

namespace App\Helpers\Requests;

use Exception;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\Cookie;

class NexusRequests
{
    public static function loginNexusApi() : void
    {
        try
        {
            Artisan::call('optimize');
            $auth = Http::post('https://ws-tisa-events-d195f2b32fec.herokuapp.com/events/login',
            [
                'usuario' => config('services.apinexus.usuario'),
                'password' => config('services.apinexus.password')
            ]);

            if(!$auth->successful())
            {
                throw new Exception("Hubo un error con la petici贸n de inicio de sesi贸n");
            }

            $filePath = config_path('services.php');

            $fileContents = File::get($filePath);

            $newFileContents = str_replace("'api_token' => '" . config('services.apinexus.api_token') . "'", "'api_token' => '" . $auth->json()['Token'] . "'", $fileContents);

            File::put($filePath, $newFileContents);
        }
        catch (Exception $ex)
        {
            throw new Exception($ex->getMessage());
        }
    }

    public static function sendParams($events) : void
    {
        try
        {
            Artisan::call('optimize');
            $eventsRequest = Http::withHeaders
            ([
                'Authorization' => 'Bearer '.config('services.apinexus.api_token')
            ])->post('https://ws-tisa-events-d195f2b32fec.herokuapp.com/events/send',
            [
                'events' => $events
            ]);

            if ($eventsRequest->status() == 401)
            {
                Artisan::call('update:token');
                return;
            }

            if (!$eventsRequest->successful())
            {
                Log::error($eventsRequest->body());
                return;
            }
            Log::info($eventsRequest->body());
        }
        catch (Exception $ex)
        {
            throw new Exception($ex->getMessage());
        }
    }
}
