<?php

namespace App\Console\Commands;

use App\Models\Store;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-admin';

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
        $user=User::create([
            'first_name'     => 'Administrator',
            'last_name'     => 'server',
            'phone'     => '+237675066919',
            'email'    => 'sfadmin@findkargo.com',
            'user_type'=>0,
            'role'=>'super_admin',
            'password' => Hash::make('x123456789'),
        ]);
        $store = Store::create([
            'vendor_id' => $user->id,
            'name' => 'homeStore',
            'phone' => null,
            'description' =>  null,
            'logo' => null,
            'cover_image' =>  null,
        ]);
    }
}
