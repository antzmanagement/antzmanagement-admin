<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Room;
use App\RoomType;
use App\UserType;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class RoomTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();


        $userType = UserType::where('name', 'tenant')->first();
        $tenants = $userType->users()->wherePivot('status', true)->get();

        foreach($tenants as $tenant) {

            $roomType = RoomType::find(
                $faker->randomElement([1, 2, 3])
            );
            $room = new Room();
            $room->uid = Carbon::now()->timestamp . Room::count();
            $room->name = $faker->ean13;
            $room->price = $roomType->price;
            $room->address = $faker->address;
            $room->state = $faker->state;
            $room->postcode = $faker->postcode;
            $room->city = $faker->city;
            $room->country = $faker->country;
            $room->save();


            $roomType->rooms()->syncWithoutDetaching([$room->refresh()->id]);
            $room->tenants()->syncWithoutDetaching([$tenant->refresh()->id]);
        }

        for($x = 0; $x < 100; $x++) {

            $roomType = RoomType::find(
                $faker->randomElement([1, 2, 3])
            );
            $room = new Room();
            $room->uid = Carbon::now()->timestamp . Room::count();
            $room->name = $faker->ean13;
            $room->price = $roomType->price;
            $room->address = $faker->address;
            $room->state = $faker->state;
            $room->postcode = $faker->postcode;
            $room->city = $faker->city;
            $room->country = $faker->country;
            $room->save();

            
            $roomType->rooms()->syncWithoutDetaching([$room->refresh()->id]);
        }

    }
}
