<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Exception;

class CacheClear extends Controller
{
    //
    public static function cacheB()
    {
        try {
            set_time_limit(120000);

            $cachePath = base_path('bootstrap/cache');

            if (File::exists($cachePath)) {
                File::deleteDirectory($cachePath);
                File::makeDirectory($cachePath);

                Log::info('Se limpio la carpeta Cache - Bootstrap');
            } else {
                Log::error('Ocurrió un error al eliminar la carpeta Cache - Bootstrap');
            }
        } catch (Exception $e) {
            Log::info($e->getMessage());
        }
    }
}
