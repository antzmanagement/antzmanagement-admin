<?php

namespace App\Console\Commands;

use App\RentalPayment;
use Illuminate\Console\Command;
use App\User;
use App\Room;
use App\RoomType;
use App\RoomCheck;
use App\RoomContract;
use App\Service;
use App\Maintenance;
use Illuminate\Support\Carbon;
use App\Traits\AllServices;
use Illuminate\Support\Facades\DB;

class Redelete extends Command
{
    use AllServices;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'redelete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Redelete the items';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        DB::beginTransaction();

        //User
        $users = User::where('status', false)->get();
        foreach($users as $user){
            $this->deleteUser($user);
        }
        
        //Room Type
        $roomTypes = RoomType::where('status', false)->get();
        foreach($roomTypes as $roomType){
            $this->deleteRoomType($roomType);
        }
        //Service
        $services = Service::where('status', false)->get();
        foreach($services as $service){
            $this->deleteService($service);
        }
        //Room
        $rooms = Room::where('status', false)->get();
        foreach($rooms as $room){
            $this->deleteRoom($room);
        }
        //Room Check
        $roomChecks = RoomCheck::where('status', false)->get();
        foreach($roomChecks as $roomCheck){
            $this->deleteRoomCheck($roomCheck);
        }
        //Room Contracts
        $roomContracts = RoomContract::where('status', false)->get();
        foreach($roomContracts as $roomContract){
            $this->deleteRoomContract($roomContract);
        }
        //Maintenances
        $maintenances = Maintenance::where('status', false)->get();
        foreach($maintenances as $maintenance){
            $this->deleteMaintenance($maintenance);
        }
        
        DB::commit();
        $this->info('Delete Done');
    }
}
