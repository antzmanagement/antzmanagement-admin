<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
       
        $this->call(UserTypeTableSeeder::class);
        $this->call(UserTableSeeder::class);

        $this->call(ServiceTableSeeder::class);
        $this->call(PropertyTableSeeder::class);
        $this->call(RoomTypeTableSeeder::class);
        $this->call(RoomTableSeeder::class);
        $this->call(MaintenanceTableSeeder::class);
    }
}
