<?php

use Illuminate\Database\Seeder;
use App\User;
use Illuminate\Support\Facades\Hash;
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = [
            [
               'name'=>'Admin',
               'photo_url'=>'user.png',
               'email'=>'admin@gmail.com',               
               'password'=>bcrypt('12345678'),
               'role_id'=>'1',
            ],
            [
               'name'=>'driver',
               'photo_url'=>'user.png',
               'email'=>'driver@gmail.com',               
               'password'=> bcrypt('12345678'),
               'role_id'=>'2',
            ]
        ];

        foreach ($user as $key => $value) {
            User::create($value);
        }
        
    }
}
