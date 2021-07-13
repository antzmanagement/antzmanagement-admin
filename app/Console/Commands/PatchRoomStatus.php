<?php

namespace App\Console\Commands;

use App\RentalPayment;
use Illuminate\Console\Command;
use App\Room;
use Illuminate\Support\Carbon;
use App\Traits\AllServices;
use Illuminate\Support\Facades\DB;

class PatchRoomStatus extends Command
{
    use AllServices;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'patch:room_status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Patch Room Empty Status to Vacant';

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
        $rooms = Room::where('status', true)->get();

        foreach ($rooms as $room) {  
            $status = $room->room_status;
            $contracts = $room->roomcontracts()->where('status', true)->where('checkedout', false)->count();
            if($contracts > 0){
                $status = 'occupied';
            }else{
                $status = 'vacant';
            }
    
            $maintenances = $room->maintenances()->where('status', true)->where('maintenance_status', 'inprogress')->count();
            if($maintenances > 0){
                $status = 'maintaining';
            }

            $room->room_status = $status;
            $room = $this->updateRoom($room, $room);
        }

        DB::commit();
        $this->info('Done Patching '. $rooms->count() . ' rooms status');
    }
}
