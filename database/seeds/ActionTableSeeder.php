<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Action;
use App\Module;
use Carbon\Carbon;

class ActionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        $modules = Module::where('status', true)->get();
        foreach ($modules as $module) {

            $action = new Action();
            $action->uid = Carbon::now()->timestamp . Action::count();
            $action->name = 'index';
            $action->desc = $faker->paragraph();
            $action->module()->associate($module);
            $action->save();


            $action = new Action();
            $action->uid = Carbon::now()->timestamp . Action::count();
            $action->name = 'filter';
            $action->desc = $faker->paragraph();
            $action->module()->associate($module);
            $action->save();

            $action = new Action();
            $action->uid = Carbon::now()->timestamp . Action::count();
            $action->name = 'show';
            $action->desc = $faker->paragraph();
            $action->module()->associate($module);
            $action->save();

            $action = new Action();
            $action->uid = Carbon::now()->timestamp . Action::count();
            $action->name = 'store';
            $action->desc = $faker->paragraph();
            $action->module()->associate($module);
            $action->save();

            $action = new Action();
            $action->uid = Carbon::now()->timestamp . Action::count();
            $action->name = 'update';
            $action->desc = $faker->paragraph();
            $action->module()->associate($module);
            $action->save();
            
            $action = new Action();
            $action->uid = Carbon::now()->timestamp . Action::count();
            $action->name = 'destroy';
            $action->desc = $faker->paragraph();
            $action->module()->associate($module);
            $action->save();
        }
    }
}
