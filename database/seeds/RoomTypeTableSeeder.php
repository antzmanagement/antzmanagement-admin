<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\RoomType;
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
        $roomType->name = 'normal';
        $roomType->price = 210.00;
        $roomType->save();

        $roomType = new RoomType();
        $roomType->uid = Carbon::now()->timestamp . RoomType::count();
        $roomType->name = 'premium';
        $roomType->price = 290.00;
        $roomType->save();

        $roomType = new RoomType();
        $roomType->uid = Carbon::now()->timestamp . RoomType::count();
        $roomType->name = 'owner';
        $roomType->price = 0.00;
        $roomType->save();
    }
}
