<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\UserType;
use Carbon\Carbon;

class UserTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        $userType = new UserType();
        $userType->uid = Carbon::now()->timestamp . UserType::count();
        $userType->name = 'tenant';
        $userType->save();
        
        $userType = new UserType();
        $userType->uid = Carbon::now()->timestamp . UserType::count();
        $userType->name = 'management';
        $userType->save();
        
        $userType = new UserType();
        $userType->uid = Carbon::now()->timestamp . UserType::count();
        $userType->name = 'owner';
        $userType->save();

    }
}
