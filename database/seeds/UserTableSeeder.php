<?php

use App\Role;
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

        $roles = Role::where('status', true)->get();

        foreach($roles as $role){
            $user = new User();
            $user->uid = Carbon::now()->timestamp . User::count();
            $user->name = $role->name;
            $user->icno = '111111-11-1111';
            $user->email = $role->name .'@gmail.com';
            $user->password = Hash::make('111111');

            $user->role()->associate($role);
            $user->save();

            $userType = UserType::where('name', 'staff')->first();
            $userType->users()->syncWithoutDetaching([$user->refresh()->id]);
        }

    }
}
