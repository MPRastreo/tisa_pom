<?php

namespace App\Helpers\Utils;

use App\Helpers\Requests\NexusRequests;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use stdClass;

class Events
{
    public static function mapData(array $eventsWialon) : array
    {
        $arrayNewItems = [];

        foreach ($eventsWialon as $item)
        {
            $newItem = new stdClass();

            $newItem->nombre = $item->d->nm;

            // Engine state
            if(isset($item->d->prms->io_239->v))
            {
                $newItem->estado_motor = $item->d->prms->io_239->v;
            }
            else if(isset($item->d->sens->{'7'}->n) && strcmp($item->d->sens->{'7'}->n, "Ignicion") == 0 && isset($item->d->prms->{$item->d->sens->{'7'}->p}->v))
            {
                $newItem->estado_motor = $item->d->prms->{$item->d->sens->{'7'}->p}->v == 0 ? 0 : 1;
            }
            elseif(isset($item->d->lmsg->p->param239))
            {
                $newItem->estado_motor = $item->d->lmsg->p->param239;
            }
            else
            {
                $newItem->estado_motor = 0;
            }

            // Panic button
            if(isset($item->d->prms->io_2->v))
            {
                $newItem->boton_panico = $item->d->prms->io_2->v;
            }
            else if(isset($item->d->sens->{'16'}->n) && strcmp($item->d->sens->{'16'}->n, "Panico") == 0 && isset($item->d->prms->in1->v))
            {
                $newItem->boton_panico = $item->d->prms->in1->v;
            }
            else if(isset($item->d->sens->{'21'}->n) && strcmp($item->d->sens->{'21'}->n, "Botón de panico") == 0 && isset($item->d->prms->in2->v))
            {
                $newItem->boton_panico = $item->d->prms->in2->v;
            }
            else
            {
                $newItem->boton_panico = 0;
            }

            // Principal battery
            if(isset($item->d->lmsg->p->power))
            {
                $newItem->bateria_principal = floatval(number_format($item->d->lmsg->p->power * 0.001, 2, '.', ''));
            }
            elseif(isset($item->d->lmsg->p->s_asgn2))
            {
                $newItem->bateria_principal = $item->d->lmsg->p->s_asgn2;
            }
            elseif(isset($item->d->lmsg->p->pwr_ext))
            {
                $newItem->bateria_principal = $item->d->lmsg->p->pwr_ext;
            }
            elseif(isset($item->d->prms->s_asgn2))
            {
                $newItem->bateria_principal = $item->d->prms->s_asgn2->v;
            }
            else
            {
                $newItem->bateria_principal = 0;
            }

            // Backup battery
            if(isset($item->d->prms->pwr_int->v))
            {
                $newItem->bateria_externa = $item->d->prms->pwr_int->v;
            }
            elseif(isset($item->d->lmsg->p->pwr_int))
            {
                $newItem->bateria_externa = $item->d->lmsg->p->pwr_int;
            }
            elseif(isset($item->d->prms->pwr_int->v))
            {
                $newItem->bateria_externa = $item->d->prms->pwr_int->v;
            }
            elseif(isset($item->d->prms->battery->v))
            {
                $newItem->bateria_externa = floatval(number_format($item->d->prms->battery->v * 0.001, 2, '.', ''));
            }
            elseif(isset($item->d->prms->s_asgn1->v))
            {
                $newItem->bateria_externa = $item->d->prms->s_asgn1->v;
            }
            elseif(isset($item->d->lmsg->p->battery->v))
            {
                $newItem->bateria_externa = floatval(number_format($item->d->lmsg->p->battery->v * 0.001, 2, '.', ''));
            }
            elseif(isset($item->d->lmsg->p->battery))
            {
                $newItem->bateria_externa = floatval(number_format($item->d->lmsg->p->battery * 0.001, 2, '.', ''));
            }

            // GPS Signal (GPS Antenna Connection)
            $newItem->conexion_antena_gps = $item->d->pos->sc;

            // Adding extra data for event request
            $newItem->device_id = $item->d->uid;
            $newItem->lat = $item->d->pos->y;
            $newItem->lon = $item->d->pos->x;
            $newItem->speed = $item->d->pos->s;

            array_push($arrayNewItems, $newItem);
        }

        return $arrayNewItems;
    }

    public static function getEvents(array $eventsWialon, string $_UID) : void
    {
        $unitsEvents = [];
        foreach ($eventsWialon as $item)
        {
            $eventDatabase = Event::where('unit_name', '=', $item->nombre)->first();
            $events = [];

            if(isset($eventDatabase))
            {
                if(!$eventDatabase->status_events)
                {
                    continue;
                }

                $eventCodes =
                [
                    [ '501' => 'MOTOR APAGADO' ],
                    [ '502' => 'MOTOR ENCENDIDO' ],
                    [ '503' => 'AUTO REPORTE OFF' ],
                    [ '504' => 'AUTO REPORTE ON' ],
                    [ '509' => 'BOTON DE PANICO' ],
                    [ '521' => 'BATERIA DE RESPALDO BAJA' ],
                    [ '523' => 'BATERIA DE RESPALDO CONECTADA' ],
                    [ '524' => 'CONEXION DE ANTENA GPS' ],
                    [ '525' => 'CONEXION DE ENERGIA PRINCIPAL' ],
                    [ '526' => 'DESCONEXION DE ANTENA GPS' ],
                    [ '527' => 'DESCONEXION DE ENERGIA PRINCIPAL' ],
                    [ '528' => 'DISPOSITIVO APAGADO' ],
                    [ '529' => 'DISPOSITIVO ENCENDIDO' ],
                    [ '530' => 'FIN DE JAMMING' ],
                    [ '531' => 'INICIO DE JAMMING' ],
                    [ '532' => 'CORTE DE MOTOR ACTIVADO' ],
                    [ '533' => 'CORTE DE MOTOR DESACTIVADO' ],
                ];

                // Add events to array
                if($eventDatabase->engine_status != $item->estado_motor)
                {
                    if($item->estado_motor == 0)
                    {
                        array_push($events, $eventCodes[0]);
                    }
                    else if($item->estado_motor == 1)
                    {
                        array_push($events, $eventCodes[1]);
                    }
                }

                ($eventDatabase->panic_button != $item->boton_panico) ? array_push($events, $eventCodes[4]) : null;

                if(number_format($eventDatabase->main_battery, 0) != number_format($item->bateria_principal, 0))
                {
                    if($item->bateria_principal > 8)
                    {
                        array_push($events, $eventCodes[8]);
                    }
                    else if($item->bateria_principal < 8)
                    {
                        array_push($events, $eventCodes[10]);
                    }
                }

                if(number_format($eventDatabase->ext_battery, 0) != number_format($item->bateria_externa, 0))
                {
                    if($item->bateria_externa > 3)
                    {
                        array_push($events, $eventCodes[6]);
                    }
                    else if($item->bateria_externa < 3)
                    {
                        array_push($events, $eventCodes[5]);
                    }
                }

                if(number_format($eventDatabase->gps_antenna, 0) != number_format($item->conexion_antena_gps, 0))
                {
                    // $diff = abs(number_format($item->conexion_antena_gps, 0) - number_format($eventDatabase->gps_antenna, 0));

                    // if(number_format($item->conexion_antena_gps, 0) > 1 && $diff >= 4)
                    if(number_format($item->conexion_antena_gps, 0) > 1)
                    {
                        array_push($events, $eventCodes[7]);
                    }
                    else if(number_format($item->conexion_antena_gps, 0) == 0)
                    {
                        array_push($events, $eventCodes[9]);
                    }
                }

                if (count($events) > 0)
                {
                    foreach ($events as $event)
                    {
                        $eventDetails = new stdClass();
                        $eventDetails->device_id = $item->device_id;
                        $eventDetails->alias = $eventDatabase->unit_name;
                        $eventDetails->event_time = Carbon::now()->toDateTimeString();
                        $eventDetails->lat = $item->lat;
                        $eventDetails->lon = $item->lon;
                        $eventDetails->speed = $item->speed;
                        $eventDetails->even = array_keys($event)[0];

                        array_push($unitsEvents, $eventDetails);
                    }
                }
            }
        }

        count($unitsEvents) > 0 ?  Log::info($unitsEvents) : null;
    }

    public static function saveDataEvents(array $arrayEvents) : void
    {
        DB::transaction(function () use($arrayEvents)
        {
            foreach ($arrayEvents as $event)
            {
                $eventDatabase = Event::where('unit_name', '=', $event->nombre)->first();
                if(!isset($eventDatabase))
                {
                    $eventDatabase = new Event();
                    $eventDatabase->status_events = 1;
                }

                $eventDatabase->unit_name = $event->nombre;
                $eventDatabase->engine_status = $event->estado_motor;
                $eventDatabase->panic_button = $event->boton_panico;
                $eventDatabase->main_battery = $event->bateria_principal;
                $eventDatabase->ext_battery = $event->bateria_externa;
                $eventDatabase->gps_antenna = $event->conexion_antena_gps;
                $eventDatabase->engine_cutoff = null;
                $eventDatabase->jamming = null;
                $eventDatabase->save();
            }
        });
    }
}
