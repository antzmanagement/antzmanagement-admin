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
        $rooms = Room::where('room_status', 'empty')->get();

        foreach ($rooms as $room) {
            $room->room_status = 'vacant';
            $room = $this->updateRoom($room, $room);
        }

        DB::commit();
        $this->info('Done Patching '. $rooms->count() . ' rooms status');
    }
}
