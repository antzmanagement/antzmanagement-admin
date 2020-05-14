<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Maintenance;
use App\Room;
use App\Property;
use Carbon\Carbon;

class MaintenanceTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();


        $rooms = Room::where('status', true)->get();
        $properties = Property::where('status', true)->get();

        foreach ($rooms as $room) {

            $count =  $faker->numberBetween(1, 15);
            for($x = 0 ; $x < $count; $x++){

                $maintenance = new Maintenance();
                $maintenance->uid = Carbon::now()->timestamp . Maintenance::count();
                $maintenance->remark = $faker->ean13;
                $maintenance->price = $faker->randomNumber(3);
                $maintenance->remark = $faker->paragraph();
                $maintenance->room()->associate($room);
    
                $property =  $properties[$faker->numberBetween(0, $properties->count() - 1)];
                $maintenance->property()->associate($property);
                $maintenance->save();
            }

        }

    }
}
