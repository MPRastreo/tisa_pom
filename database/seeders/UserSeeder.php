<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create
        ([
            'name' => 'Lab Dev',
            'email' => 'lab@satech.mx',
            'password' => bcrypt('mp-rs@1234')
        ]);

        // User::create
        // ([
        //     'name' => 'Test Nexus',
        //     'email' => 'test@ftnexus.com.mx',
        //     'password' => bcrypt('test@1234')
        // ]);

        // User::create
        // ([
        //     'name' => 'Admin TISA',
        //     'email' => 'admin@wstisa.com',
        //     'password' => bcrypt('tisa@2023')
        // ]);
    }
}
