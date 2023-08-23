<?php

namespace App\Http\Controllers;

use App\Helpers\Requests\WialonRequests;
use App\Helpers\Utils\Events;
use App\Models\Event;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class EventController extends Controller
{
    public static function requestUnits()
    {
        try
        {
            set_time_limit(160);

            //Login in Wialon API
            $sessionWialon = WialonRequests::loginWialon();

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
            Log::error($ex->getTraceAsString());
        }
    }

    public function toggleStatus($id)
    {
        try
        {
            $unit = Event::find($id);
            $unit->status_events = !$unit->status_events;
            $unit->save();

            return redirect()->back()->with('success', 'Se modificó el estatus de los eventos de la unidad');
        }
        catch (Exception $ex)
        {
            return Redirect::back()->withErrors(['message' => 'Hubo un error inesperado']);
        }
    }
}
