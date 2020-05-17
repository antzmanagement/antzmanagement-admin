<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Contract;
use App\Room;
use App\Property;
use Carbon\Carbon;

class ContractTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $faker = Faker::create();
        $contract = new Contract();
        $contract->uid = Carbon::now()->timestamp . Contract::count();
        $contract->name = "1 Year Contract";
        $contract->duration = 12;
        $contract->terms = $faker->paragraph(10);
        $contract->autorenew = true;
        $contract->save();

        $faker = Faker::create();
        $contract = new Contract();
        $contract->uid = Carbon::now()->timestamp . Contract::count();
        $contract->name = "2 Year Contract";
        $contract->duration = 24;
        $contract->terms = $faker->paragraph(10);
        $contract->autorenew = true;
        $contract->save();
        
        $faker = Faker::create();
        $contract = new Contract();
        $contract->uid = Carbon::now()->timestamp . Contract::count();
        $contract->name = "3 Year Contract";
        $contract->duration = 36;
        $contract->terms = $faker->paragraph(10);
        $contract->autorenew = true;
        $contract->save();
        
        $faker = Faker::create();
        $contract = new Contract();
        $contract->uid = Carbon::now()->timestamp . Contract::count();
        $contract->name = "4 Year Contract";
        $contract->duration = 48;
        $contract->terms = $faker->paragraph(10);
        $contract->autorenew = true;
        $contract->save();
    }
}
