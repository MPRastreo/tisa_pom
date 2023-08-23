<?php

namespace App\Console\Commands;

use App\Http\Controllers\EventController;
use Illuminate\Console\Command;

class UpdateEvents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:events';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comando que actualiza y envía los eventos a la API de Nexus';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        EventController::requestUnits();
    }
}
