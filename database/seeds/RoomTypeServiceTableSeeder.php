<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\RoomType;
use App\RoomTypeService;
use Carbon\Carbon;

class RoomTypeServiceTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        $roomTypeService = new RoomTypeService();
        $roomTypeService->uid = Carbon::now()->timestamp . RoomTypeService::count();
        $roomTypeService->name = 'freeWifi';
        $roomTypeService->text = 'Free Wifi';
        $roomTypeService->desc = $faker->paragraph();
        $roomTypeService->icon = "mdi-wifi";
        $roomTypeService->save();

        
        $roomTypeService = new RoomTypeService();
        $roomTypeService->uid = Carbon::now()->timestamp . RoomTypeService::count();
        $roomTypeService->name = 'laundry';
        $roomTypeService->text = 'Laundry Services';
        $roomTypeService->desc = $faker->paragraph();
        $roomTypeService->icon = "mdi-washing-machine";
        $roomTypeService->save();

        $roomTypeService = new RoomTypeService();
        $roomTypeService->uid = Carbon::now()->timestamp . RoomTypeService::count();
        $roomTypeService->name = 'cleaning';
        $roomTypeService->text = 'Cleaning';
        $roomTypeService->desc = $faker->paragraph();
        $roomTypeService->icon = "mdi-broom";
        $roomTypeService->save();

        $roomTypeService = new RoomTypeService();
        $roomTypeService->uid = Carbon::now()->timestamp . RoomTypeService::count();
        $roomTypeService->name = 'gym';
        $roomTypeService->text = 'Gym';
        $roomTypeService->desc = $faker->paragraph();
        $roomTypeService->icon = "mdi-dumbbell";
        $roomTypeService->save();

        
        $roomTypeService = new RoomTypeService();
        $roomTypeService->uid = Carbon::now()->timestamp . RoomTypeService::count();
        $roomTypeService->name = 'parking';
        $roomTypeService->text = 'Free Parking';
        $roomTypeService->desc = $faker->paragraph();
        $roomTypeService->icon = "mdi-parking";
        $roomTypeService->save();
        
        $roomTypeService = new RoomTypeService();
        $roomTypeService->uid = Carbon::now()->timestamp . RoomTypeService::count();
        $roomTypeService->name = 'furnished';
        $roomTypeService->text = 'Fully Furnished';
        $roomTypeService->desc = $faker->paragraph();
        $roomTypeService->icon = "mdi-table-chair";
        $roomTypeService->save();

        
        $roomTypeService = new RoomTypeService();
        $roomTypeService->uid = Carbon::now()->timestamp . RoomTypeService::count();
        $roomTypeService->name = 'transportation';
        $roomTypeService->text = 'Van Transportation';
        $roomTypeService->desc = $faker->paragraph();
        $roomTypeService->icon = "mdi-van-passenger";
        $roomTypeService->save();
        
        $roomTypeService = new RoomTypeService();
        $roomTypeService->uid = Carbon::now()->timestamp . RoomTypeService::count();
        $roomTypeService->name = 'kitchen';
        $roomTypeService->text = 'Kitchen';
        $roomTypeService->desc = $faker->paragraph();
        $roomTypeService->icon = "mdi-chef-hat";
        $roomTypeService->save();
    
    }
}
