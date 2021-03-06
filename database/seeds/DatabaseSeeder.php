<?php

use Illuminate\Database\Seeder;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
       $this->call(RoleSeeder::class);
       $this->call(UserSeeder::class);
       $this->call(CarburantSeeder::class);
       $this->call(CarSeeder::class);
       $this->call(ActivitySeeder::class);
        $this->call(ReparationSeeder::class);
    }
}
