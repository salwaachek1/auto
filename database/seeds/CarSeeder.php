<?php

use Illuminate\Database\Seeder;
use App\Car;
class CarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $cars = [
            [
               'model'=>'Dacia',
               'serial_number'=>'64654654',
               'place'=>'Djerba',               
               'carburant_id'=>1,
               'kilo'=>150,
               'is_dispo'=>1,
               'is_working'=>1,
               'photo_url'=>'noimage.jpg',
            ]
        ];

        foreach ($cars as $car) {
            Car::create($car);
        }
}
}
