<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\RoomType;
use App\Service;
use Carbon\Carbon;

class ServiceTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        $service = new Service();
        $service->uid = Carbon::now()->timestamp . Service::count();
        $service->name = 'wifi';
        $service->text = 'Wifi';
        $service->desc = $faker->paragraph();
        $service->price = 38.00;
        $service->save();

        
        $service = new Service();
        $service->uid = Carbon::now()->timestamp . Service::count();
        $service->name = 'laundry';
        $service->text = 'Laundry';
        $service->desc = $faker->paragraph();
        $service->price = 43.00;
        $service->save();

        $service = new Service();
        $service->uid = Carbon::now()->timestamp . Service::count();
        $service->name = 'cleaning';
        $service->text = 'Cleaning';
        $service->desc = $faker->paragraph();
        $service->price = 43.00;
        $service->save();

        $service = new Service();
        $service->uid = Carbon::now()->timestamp . Service::count();
        $service->name = 'facilityRoom';
        $service->text = 'Facility Room';
        $service->desc = $faker->paragraph();
        $service->price = 28.00;
        $service->save();

        
        $service = new Service();
        $service->uid = Carbon::now()->timestamp . Service::count();
        $service->name = 'gym';
        $service->text = 'Gym';
        $service->desc = $faker->paragraph();
        $service->price = 38.00;
        $service->save();
        
    
    }
}
