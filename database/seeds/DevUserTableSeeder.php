<?php

use App\Role;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\User;
use App\UserType;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DevUserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();


        for ($x = 0; $x < 100; $x++) {

            $user = new User();
            $user->uid = Carbon::now()->timestamp . User::count();
            $user->name = $faker->userName;
            $user->icno = $faker->ean13;
            $user->tel1 = $faker->ean13;
            $user->email =  $faker->unique()->safeEmail;
            $user->birthday =  $faker->dateTimeBetween($startDate = '-10 years', $endDate = 'now');
            $user->password = Hash::make('111111');
            $role = Role::where('name', 'tenant')->first();
            $user->role()->associate($role);
            $user->save();

            $userType = UserType::where('name', 'tenant')->first();
            $userType->users()->syncWithoutDetaching([$user->refresh()->id]);

        }

        for ($x = 0; $x < 100; $x++) {

            $user = new User();
            $user->uid = Carbon::now()->timestamp . User::count();
            $user->name = $faker->userName;
            $user->icno = $faker->ean13;
            $user->tel1 = $faker->ean13;
            $user->email =  $faker->unique()->safeEmail;
            $user->password = Hash::make('111111');
            $role = Role::where('name', 'owner')->first();
            $user->role()->associate($role);
            $user->save();

            $userType = UserType::where('name', 'owner')->first();
            $userType->users()->syncWithoutDetaching([$user->refresh()->id]);

        }


        for ($x = 0; $x < 100; $x++) {

            $user = new User();
            $user->uid = Carbon::now()->timestamp . User::count();
            $user->name = $faker->userName;
            $user->icno = $faker->ean13;
            $user->tel1 = $faker->ean13;
            $user->email =  $faker->unique()->safeEmail;
            $user->password = Hash::make('111111');
            $role = Role::where('name', 'manager')->first();
            $user->role()->associate($role);
            $user->save();

            $userType = UserType::where('name', 'staff')->first();
            $userType->users()->syncWithoutDetaching([$user->refresh()->id]);

        }
    }
}
