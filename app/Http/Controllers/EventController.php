<?php

namespace App\Http\Controllers;

use App\Helpers\Requests\WialonRequests;
use App\Helpers\Utils\Events;
use App\Models\Event;
use App\Services\LogService;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class EventController extends Controller
{
    public static function requestUnits()
    {
        try
        {
            set_time_limit(120000);

            //Login in Wialon API
            $sessionWialon = WialonRequests::login();

            //Request to get data from units, included sensors
            $dataUnits = WialonRequests::getDataUnits($sessionWialon['_ID']);

            //Map data to return it
            $unitsMapped = Events::mapData($dataUnits);

            // Get events
            Events::getEvents($unitsMapped, $sessionWialon['_UID']);

            // Store data in db
            Events::saveDataEvents($unitsMapped);
        }
        catch (Exception $ex)
        {
            Log::warning($ex->getMessage());
            LogService::sendToLog($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function toggleStatus($id)
    {
        try
        {
            set_time_limit(120000);

            $unit = Event::find($id);
            $unit->status_events = !$unit->status_events;
            $unit->save();

            return redirect()->back()->with('success', 'Se modificó el estatus de los eventos de la unidad');
        }
        catch (Exception $ex)
        {
            LogService::sendToLog($ex->getMessage());
            return Redirect::back()->withErrors(['message' => 'Hubo un error inesperado']);
        }
    }
}
