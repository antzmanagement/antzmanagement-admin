<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\RoomType;
use App\RoomTypeProperty;
use Carbon\Carbon;

class RoomTypePropertyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        $roomTypeProperty = new RoomTypeProperty();
        $roomTypeProperty->uid = Carbon::now()->timestamp . RoomTypeProperty::count();
        $roomTypeProperty->name = 'bed';
        $roomTypeProperty->text = 'Bed';
        $roomTypeProperty->desc = $faker->paragraph();
        $roomTypeProperty->icon = "";
        $roomTypeProperty->save();

        
        $roomTypeProperty = new RoomTypeProperty();
        $roomTypeProperty->uid = Carbon::now()->timestamp . RoomTypeProperty::count();
        $roomTypeProperty->name = 'accomodate';
        $roomTypeProperty->text = 'Accommodates';
        $roomTypeProperty->desc = $faker->paragraph();
        $roomTypeProperty->icon = "";
        $roomTypeProperty->save();

        $roomTypeProperty = new RoomTypeProperty();
        $roomTypeProperty->uid = Carbon::now()->timestamp . RoomTypeProperty::count();
        $roomTypeProperty->name = 'cupboard';
        $roomTypeProperty->text = 'Cupboard';
        $roomTypeProperty->desc = $faker->paragraph();
        $roomTypeProperty->icon = "";
        $roomTypeProperty->save();

        $roomTypeProperty = new RoomTypeProperty();
        $roomTypeProperty->uid = Carbon::now()->timestamp . RoomTypeProperty::count();
        $roomTypeProperty->name = 'table';
        $roomTypeProperty->text = 'Table';
        $roomTypeProperty->desc = $faker->paragraph();
        $roomTypeProperty->icon = "";
        $roomTypeProperty->save();

        
        $roomTypeProperty = new RoomTypeProperty();
        $roomTypeProperty->uid = Carbon::now()->timestamp . RoomTypeProperty::count();
        $roomTypeProperty->name = 'chair';
        $roomTypeProperty->text = 'Chair';
        $roomTypeProperty->desc = $faker->paragraph();
        $roomTypeProperty->icon = "";
        $roomTypeProperty->save();
        
        $roomTypeProperty = new RoomTypeProperty();
        $roomTypeProperty->uid = Carbon::now()->timestamp . RoomTypeProperty::count();
        $roomTypeProperty->name = 'refrigerator';
        $roomTypeProperty->text = 'Refrigerator';
        $roomTypeProperty->desc = $faker->paragraph();
        $roomTypeProperty->icon = "";
        $roomTypeProperty->save();

        
        $roomTypeProperty = new RoomTypeProperty();
        $roomTypeProperty->uid = Carbon::now()->timestamp . RoomTypeProperty::count();
        $roomTypeProperty->name = 'heater';
        $roomTypeProperty->text = 'Heater';
        $roomTypeProperty->desc = $faker->paragraph();
        $roomTypeProperty->icon = "";
        $roomTypeProperty->save();
        
        $roomTypeProperty = new RoomTypeProperty();
        $roomTypeProperty->uid = Carbon::now()->timestamp . RoomTypeProperty::count();
        $roomTypeProperty->name = 'toilet';
        $roomTypeProperty->text = 'Toilet';
        $roomTypeProperty->desc = $faker->paragraph();
        $roomTypeProperty->icon = "";
        $roomTypeProperty->save();
    
    }
}
