<?php

use Illuminate\Database\Seeder;
use App\Carburant;
class CarburantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $carburant= [
            [
               'name'=>'Essence',
            ],
            [
               'name'=>'Diesel',
            ],
            [
                'name'=>'LPG'
            ],
            [
                'name'=>'Hybride'
            ],
            [
                'name'=>'Diesel hybride rechargeable'
            ],
            [
                'name'=>'Essence hybride rechargeable'
            ],
            [
                'name'=>'Essence hybride complet'
            ],
            [
                'name'=>'CNG'
            ]
            ,
            [
                'name'=>'Hydrogène'
            ]
        ];

        foreach ($carburant as $value) {
            Carburant::create($value);
        }
    }
}
