<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\RoomType;
use App\RoomTypeImage;
use App\Service;
use App\Property;
use Carbon\Carbon;

class RoomTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $faker = Faker::create();

        $roomType = new RoomType();
        $roomType->uid = Carbon::now()->timestamp . RoomType::count();
        $roomType->name = 'premium1';
        $roomType->text = "Premium Unit With Laundry";
        $roomType->price = 278.00;
        $roomType->desc = "";
        $roomType->save();

        $properties = Property::find([1,2,3,4,5,6,7,8,9,10,11]);
        foreach ($properties as $property) {

            $roomType->properties()->attach($property->id);
        }

        $services = Service::find([1,3,4,5]);
        foreach ($services as $service) {
            $roomType->services()->attach($service->id);
        }


        $roomType = new RoomType();
        $roomType->uid = Carbon::now()->timestamp . RoomType::count();
        $roomType->name = 'premium2';
        $roomType->text = "Premium Unit With Cleaning";
        $roomType->price = 278.00;
        $roomType->desc = "";
        $roomType->save();

        $properties = Property::find([1,2,3,4,5,6,7,8,9,10,11]);
        foreach ($properties as $property) {

            $roomType->properties()->attach($property->id);
        }

        $services = Service::find([1,2,4,5]);
        foreach ($services as $service) {
            $roomType->services()->attach($service->id);
        }
        
        $roomType = new RoomType();
        $roomType->uid = Carbon::now()->timestamp . RoomType::count();
        $roomType->name = 'premium3';
        $roomType->text = "Premium Unit With Full Package";
        $roomType->price = 298.00;
        $roomType->desc = "";
        $roomType->save();

        $properties = Property::find([1,2,3,4,5,6,7,8,9,10,11]);
        foreach ($properties as $property) {

            $roomType->properties()->attach($property->id);
        }

        $services = Service::find([1,2,3,4,5]);
        foreach ($services as $service) {
            $roomType->services()->attach($service->id);
        }
        

        $roomType = new RoomType();
        $roomType->uid = Carbon::now()->timestamp . RoomType::count();
        $roomType->name = 'wifiZone';
        $roomType->text = "Wifi Zone";
        $roomType->price = 248.00;
        $roomType->desc = "";
        $roomType->save();

        $properties = Property::find([1,2,3,4,5,6,7,8]);
        foreach ($properties as $property) {

            $roomType->properties()->attach($property->id);
        }

        $services = Service::find([1]);
        foreach ($services as $service) {
            $roomType->services()->attach($service->id);
        }
        
        $roomType = new RoomType();
        $roomType->uid = Carbon::now()->timestamp . RoomType::count();
        $roomType->name = 'basic';
        $roomType->text = "Basic";
        $roomType->price = 218.00;
        $roomType->desc = "";
        $roomType->save();

        $properties = Property::find([1,2,3,4,5,6,7,8]);
        foreach ($properties as $property) {

            $roomType->properties()->attach($property->id);
        }

        $services = Service::find([]);
        foreach ($services as $service) {
            $roomType->services()->attach($service->id);
        }
    }
}
