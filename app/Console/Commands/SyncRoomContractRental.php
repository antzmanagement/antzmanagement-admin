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

class SyncRoomContractRental extends Command
{
    use AllServices;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:room_contract_rental';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'SyncRoomContractRental';

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

        //Room Contracts
        $roomContracts = RoomContract::where('status', true)->get();
        foreach($roomContracts as $roomContract){
            $this->syncWithRentalPayment($roomContract);
        }
        DB::commit();
        $this->info('Sync Done');
    }
}
