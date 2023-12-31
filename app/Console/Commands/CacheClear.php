<?php

namespace App\Console\Commands;

use App\Http\Controllers\CacheClear as ControllersCacheClear;
use Illuminate\Console\Command;

class CacheClear extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cache-clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        ControllersCacheClear::cacheB();
    }
}
