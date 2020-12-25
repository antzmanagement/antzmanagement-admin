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
        $contract->name = "T&C Contract";
        $contract->duration = 12;
        $contract->autorenew = true;
        $contract->rental_type = 'month';
        $contract->penalty_day = 10;
        $contract->penalty = 3;
        $contract->save();

        $faker = Faker::create();
        $contract = new Contract();
        $contract->uid = Carbon::now()->timestamp . Contract::count();
        $contract->name = "Homestay";
        $contract->duration = 1;
        $contract->autorenew = false;
        $contract->rental_type = 'day';
        $contract->penalty_day = 0;
        $contract->penalty = 0;
        $contract->save();
    }
}
