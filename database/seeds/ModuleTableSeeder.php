<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Module;
use Carbon\Carbon;

class ModuleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        $module = new Module();
        $module->uid = Carbon::now()->timestamp . Module::count();
        $module->name = 'module';
        $module->desc = $faker->paragraph();
        $module->save();
        
        $module = new Module();
        $module->uid = Carbon::now()->timestamp . Module::count();
        $module->name = 'role';
        $module->desc = $faker->paragraph();
        $module->save();

        $module = new Module();
        $module->uid = Carbon::now()->timestamp . Module::count();
        $module->name = 'staff';
        $module->desc = $faker->paragraph();
        $module->save();

        
        $module = new Module();
        $module->uid = Carbon::now()->timestamp . Module::count();
        $module->name = 'tenant';
        $module->desc = $faker->paragraph();
        $module->save();
        
        $module = new Module();
        $module->uid = Carbon::now()->timestamp . Module::count();
        $module->name = 'owner';
        $module->desc = $faker->paragraph();
        $module->save();
        
        $module = new Module();
        $module->uid = Carbon::now()->timestamp . Module::count();
        $module->name = 'contract';
        $module->desc = $faker->paragraph();
        $module->save();

        $module = new Module();
        $module->uid = Carbon::now()->timestamp . Module::count();
        $module->name = 'roomtype';
        $module->desc = $faker->paragraph();
        $module->save();

        $module = new Module();
        $module->uid = Carbon::now()->timestamp . Module::count();
        $module->name = 'room';
        $module->desc = $faker->paragraph();
        $module->save();
        
        $module = new Module();
        $module->uid = Carbon::now()->timestamp . Module::count();
        $module->name = 'maintenance';
        $module->desc = $faker->paragraph();
        $module->save();
        
        $module = new Module();
        $module->uid = Carbon::now()->timestamp . Module::count();
        $module->name = 'property';
        $module->desc = $faker->paragraph();
        $module->save();

        $module = new Module();
        $module->uid = Carbon::now()->timestamp . Module::count();
        $module->name = 'service';
        $module->desc = $faker->paragraph();
        $module->save();

        $module = new Module();
        $module->uid = Carbon::now()->timestamp . Module::count();
        $module->name = 'contract';
        $module->desc = $faker->paragraph();
        $module->save();
    }
}
