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
        $service->name = 'freeWifi';
        $service->text = 'Free Wifi';
        $service->desc = $faker->paragraph();
        $service->icon = "mdi-wifi";
        $service->save();

        
        $service = new Service();
        $service->uid = Carbon::now()->timestamp . Service::count();
        $service->name = 'laundry';
        $service->text = 'Laundry Services';
        $service->desc = $faker->paragraph();
        $service->icon = "mdi-washing-machine";
        $service->save();

        $service = new Service();
        $service->uid = Carbon::now()->timestamp . Service::count();
        $service->name = 'cleaning';
        $service->text = 'Cleaning';
        $service->desc = $faker->paragraph();
        $service->icon = "mdi-broom";
        $service->save();

        $service = new Service();
        $service->uid = Carbon::now()->timestamp . Service::count();
        $service->name = 'gym';
        $service->text = 'Gym';
        $service->desc = $faker->paragraph();
        $service->icon = "mdi-dumbbell";
        $service->save();

        
        $service = new Service();
        $service->uid = Carbon::now()->timestamp . Service::count();
        $service->name = 'parking';
        $service->text = 'Free Parking';
        $service->desc = $faker->paragraph();
        $service->icon = "mdi-parking";
        $service->save();
        
        $service = new Service();
        $service->uid = Carbon::now()->timestamp . Service::count();
        $service->name = 'furnished';
        $service->text = 'Fully Furnished';
        $service->desc = $faker->paragraph();
        $service->icon = "mdi-table-chair";
        $service->save();

        
        $service = new Service();
        $service->uid = Carbon::now()->timestamp . Service::count();
        $service->name = 'transportation';
        $service->text = 'Van Transportation';
        $service->desc = $faker->paragraph();
        $service->icon = "mdi-van-passenger";
        $service->save();
        
        $service = new Service();
        $service->uid = Carbon::now()->timestamp . Service::count();
        $service->name = 'kitchen';
        $service->text = 'Kitchen';
        $service->desc = $faker->paragraph();
        $service->icon = "mdi-chef-hat";
        $service->save();
    
    }
}
