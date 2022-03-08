<?php

use Illuminate\Database\Seeder;
use App\Reparation;
class ReparationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $reparations = [
            [
               'garage'=>'anonyme',
               'diagnosis'=>'description de panne .. ',
               'is_done'=>0,
               'car_id'=>1,  
               'replaced_parts'=>'les parties enlevées de la voiture', 
               'fees'=>200,
               'date_out'=>'2022-02-18 10:43:27',
               'phone'=>'23456842'
            ]
        ];

        foreach ($reparations as $rep) {
            Reparation::create($rep);
        }
    }
}
