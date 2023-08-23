<?php

namespace App\Console\Commands;

use App\Helpers\Requests\NexusRequests;
use Illuminate\Console\Command;

class UpdateToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:token';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comando que intenta loguearse en la API de Nexus para mantener el token de acceso actualizado';

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
        NexusRequests::loginNexusApi();
    }
}
