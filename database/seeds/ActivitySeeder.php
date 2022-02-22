<?php

use Illuminate\Database\Seeder;
use App\Activity;
class ActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $activities = [
            [
               'user_id'=>1,
               'car_id'=>1,
               'is_done'=>1,
               'before_photo_url'=>'noimage.jpg',  
               'after_photo_url'=>'noimage.jpg', 
               'before_kilos'=>100,
               'after_kilos'=>150,
               'expenses'=>20,
               'fuel'=>5,
               'previous_fuel_amount'=>5,  
               'after_fuel_amount'=>2,
               'destination'=>'tunis',
               'returning_date'=>'2022-02-18 10:43:27'
            ]
        ];

        foreach ($activities as $act) {
            Activity::create($act);
        }
    }
}
