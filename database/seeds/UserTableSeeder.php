<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\User;
use App\UserType;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        $user = new User();
        $user->uid = Carbon::now()->timestamp . User::count();
        $user->name = 'admin';
        $user->icno = '111111-11-1111';
        $user->email = 'admin@gmail.com';
        $user->password = Hash::make('111111');
        $user->save();

        $userType = UserType::where('name', 'management')->first();
        $userType->users()->syncWithoutDetaching([$user->refresh()->id]);

        for ($x = 0; $x < 100; $x++) {

            $user = new User();
            $user->uid = Carbon::now()->timestamp . User::count();
            $user->name = $faker->userName;
            $user->icno = $faker->ean13;
            $user->tel1 = $faker->ean13;
            $user->email =  $faker->unique()->safeEmail;
            $user->password = Hash::make('111111');
            $user->save();
            
            $userType = UserType::where('name', 'tenant')->first();
            $userType->users()->syncWithoutDetaching([$user->refresh()->id]);
        }
    }
}
