<?php

use App\Module;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Role;
use Carbon\Carbon;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        $role = new Role();
        $role->uid = Carbon::now()->timestamp . Role::count();
        $role->name = 'superadmin';
        $role->desc = $faker->paragraph();
        $role->save();

        $modules = Module::where('status', true)->get();
        foreach($modules as $module){
            $module->roles()->attach($role->refresh()->id);
        }
        
        $role = new Role();
        $role->uid = Carbon::now()->timestamp . Role::count();
        $role->name = 'admin';
        $role->desc = $faker->paragraph();
        $role->save();


        $modules = Module::where('name', '!=', 'module')->where('name', '!=', 'role')->where('name', '!=', 'contract')->where('status', true)->get();
        foreach($modules as $module){
            $module->roles()->attach($role->refresh()->id);
        }
        
        $role = new Role();
        $role->uid = Carbon::now()->timestamp . Role::count();
        $role->name = 'manager';
        $role->desc = $faker->paragraph();
        $role->save();
        
        $role = new Role();
        $role->uid = Carbon::now()->timestamp . Role::count();
        $role->name = 'cashier';
        $role->desc = $faker->paragraph();
        $role->save();
        
        $role = new Role();
        $role->uid = Carbon::now()->timestamp . Role::count();
        $role->name = 'tenant';
        $role->desc = $faker->paragraph();
        $role->save();
        
        $role = new Role();
        $role->uid = Carbon::now()->timestamp . Role::count();
        $role->name = 'owner';
        $role->desc = $faker->paragraph();
        $role->save();
    
    }
}
