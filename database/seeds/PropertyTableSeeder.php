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
        $property->save();

        
        $property = new Property();
        $property->uid = Carbon::now()->timestamp . Property::count();
        $property->name = 'accomodate';
        $property->text = 'Accommodates';
        $property->save();

        $property = new Property();
        $property->uid = Carbon::now()->timestamp . Property::count();
        $property->name = 'cupboard';
        $property->text = 'Cupboard';
        $property->save();

        $property = new Property();
        $property->uid = Carbon::now()->timestamp . Property::count();
        $property->name = 'table';
        $property->text = 'Table';
        $property->save();

        
        $property = new Property();
        $property->uid = Carbon::now()->timestamp . Property::count();
        $property->name = 'chair';
        $property->text = 'Chair';
        $property->save();
        
        $property = new Property();
        $property->uid = Carbon::now()->timestamp . Property::count();
        $property->name = 'refrigerator';
        $property->text = 'Refrigerator';
        $property->save();

        
        $property = new Property();
        $property->uid = Carbon::now()->timestamp . Property::count();
        $property->name = 'heater';
        $property->text = 'Heater';
        $property->save();
        
        $property = new Property();
        $property->uid = Carbon::now()->timestamp . Property::count();
        $property->name = 'toilet';
        $property->text = 'Toilet';
        $property->save();
        
        $property = new Property();
        $property->uid = Carbon::now()->timestamp . Property::count();
        $property->name = 'ceilingFan';
        $property->text = 'Ceiling Fan';
        $property->save();
        
        $property = new Property();
        $property->uid = Carbon::now()->timestamp . Property::count();
        $property->name = 'wallFan';
        $property->text = 'Wall Fan';
        $property->save();
        
        $property = new Property();
        $property->uid = Carbon::now()->timestamp . Property::count();
        $property->name = 'frontGrillDoor';
        $property->text = 'Front Grill Door';
        $property->save();
    
        $property = new Property();
        $property->uid = Carbon::now()->timestamp . Property::count();
        $property->name = 'backGrillDoor';
        $property->text = 'Back Grill Door';
        $property->save();

        $property = new Property();
        $property->uid = Carbon::now()->timestamp . Property::count();
        $property->name = 'airCond';
        $property->text = 'Air Conditioner';
        $property->save();

        $property = new Property();
        $property->uid = Carbon::now()->timestamp . Property::count();
        $property->name = 'mattress';
        $property->text = 'Mattress';
        $property->save();
    }
}
