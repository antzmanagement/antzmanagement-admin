<?php

use App\Contract;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Room;
use App\RoomContract;
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

        foreach ($tenants as $tenant) {

            $roomType = RoomType::find(
                $faker->randomElement([1,2,3,4,5])
            );
            $room = new Room();
            $room->uid = Carbon::now()->timestamp . Room::count();
            $room->name = $roomType->text . Room::count();
            $room->price = $roomType->price;
            $room->address = $faker->address;
            $room->state = $faker->state;
            $room->postcode = $faker->postcode;
            $room->city = $faker->city;
            $room->country = $faker->country;
            $room->save();


            $roomType->rooms()->syncWithoutDetaching([$room->refresh()->id]);

            //Create Contract For Tenant
            $contract = Contract::find($faker->randomElement([1, 2, 3]));
            
            $roomcontract = new RoomContract();
            $roomcontract->uid = Carbon::now()->timestamp . RoomContract::count();
            $roomcontract->name = $contract->name;
            $roomcontract->duration = $contract->duration;
            $roomcontract->terms = $contract->terms;
            $roomcontract->autorenew = $contract->autorenew;
            $roomcontract->leftmonth = $contract->duration;
            $roomcontract->startdate = Carbon::now()->addMonth($faker->numberBetween(-36,36))->startOfMonth();
            $roomcontract->room()->associate($room);
            $roomcontract->tenant()->associate($tenant);
            $roomcontract->contract()->associate($contract);
            $roomcontract->save();

            
            //Assign Owner For Tenant
            if ($faker->boolean()) {

                $userType = UserType::where('name', 'owner')->first();
                $owners = $userType->users()->wherePivot('status', true)->get();
                $owner = $owners->random();

                $room->owners()->syncWithoutDetaching([$owner->refresh()->id]);
            }
        }


        $userType = UserType::where('name', 'owner')->first();
        $owners = $userType->users()->wherePivot('status', true)->get();

        foreach ($owners as $owner) {

            $roomType = RoomType::find(
                $faker->randomElement([1, 2, 3])
            );
            $room = new Room();
            $room->uid = Carbon::now()->timestamp . Room::count();
            $room->name = $roomType->text . Room::count();
            $room->price = $roomType->price;
            $room->address = $faker->address;
            $room->state = $faker->state;
            $room->postcode = $faker->postcode;
            $room->city = $faker->city;
            $room->country = $faker->country;
            $room->save();


            $roomType->rooms()->syncWithoutDetaching([$room->refresh()->id]);
            $room->owners()->syncWithoutDetaching([$owner->refresh()->id]);
        }

        for ($x = 0; $x < 100; $x++) {

            $roomType = RoomType::find(
                $faker->randomElement([1, 2, 3])
            );
            $room = new Room();
            $room->uid = Carbon::now()->timestamp . Room::count();
            $room->name = $roomType->text . Room::count();
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
