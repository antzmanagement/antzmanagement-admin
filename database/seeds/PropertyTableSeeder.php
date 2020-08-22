<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\RoomType;
use App\Property;
use Carbon\Carbon;

class PropertyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        $property = new Property();
        $property->uid = Carbon::now()->timestamp . Property::count();
        $property->name = 'bed';
        $property->text = 'Bed';
        $property->desc = $faker->paragraph();
        $property->save();

        
        $property = new Property();
        $property->uid = Carbon::now()->timestamp . Property::count();
        $property->name = 'accomodate';
        $property->text = 'Accommodates';
        $property->desc = $faker->paragraph();
        $property->save();

        $property = new Property();
        $property->uid = Carbon::now()->timestamp . Property::count();
        $property->name = 'cupboard';
        $property->text = 'Cupboard';
        $property->desc = $faker->paragraph();
        $property->save();

        $property = new Property();
        $property->uid = Carbon::now()->timestamp . Property::count();
        $property->name = 'table';
        $property->text = 'Table';
        $property->desc = $faker->paragraph();
        $property->save();

        
        $property = new Property();
        $property->uid = Carbon::now()->timestamp . Property::count();
        $property->name = 'chair';
        $property->text = 'Chair';
        $property->desc = $faker->paragraph();
        $property->save();
        
        $property = new Property();
        $property->uid = Carbon::now()->timestamp . Property::count();
        $property->name = 'refrigerator';
        $property->text = 'Refrigerator';
        $property->desc = $faker->paragraph();
        $property->save();

        
        $property = new Property();
        $property->uid = Carbon::now()->timestamp . Property::count();
        $property->name = 'heater';
        $property->text = 'Heater';
        $property->desc = $faker->paragraph();
        $property->save();
        
        $property = new Property();
        $property->uid = Carbon::now()->timestamp . Property::count();
        $property->name = 'toilet';
        $property->text = 'Toilet';
        $property->desc = $faker->paragraph();
        $property->save();
        
        $property = new Property();
        $property->uid = Carbon::now()->timestamp . Property::count();
        $property->name = 'ceilingFan';
        $property->text = 'Ceiling Fan';
        $property->desc = $faker->paragraph();
        $property->price = 28.00;
        $property->save();
        
        $property = new Property();
        $property->uid = Carbon::now()->timestamp . Property::count();
        $property->name = 'wallFan';
        $property->text = 'Wall Fan';
        $property->desc = $faker->paragraph();
        $property->price = 28.00;
        $property->save();
        
        $property = new Property();
        $property->uid = Carbon::now()->timestamp . Property::count();
        $property->name = 'ironDoor';
        $property->text = 'Iron Door';
        $property->desc = $faker->paragraph();
        $property->price = 28.00;
        $property->save();
    
    }
}
