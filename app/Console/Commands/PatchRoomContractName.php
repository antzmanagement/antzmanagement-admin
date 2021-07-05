<?php

namespace App\Console\Commands;

use App\RoomContract;
use Illuminate\Console\Command;
use App\Room;
use Illuminate\Support\Carbon;
use App\Traits\AllServices;
use Illuminate\Support\Facades\DB;

class PatchRoomContractName extends Command
{
    use AllServices;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'patch:room_contract_name';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Patch Room Contract Name';

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
        $data = RoomContract::where('status', true)->get();

        foreach ($data as $item) {
            $startdate = Carbon::parse($item->startdate)->format('Y-m-d');
            $enddate = Carbon::parse($item->enddate)->format('Y-m-d');
            $item->name = $startdate. ' - ' . $enddate;
            $this->updateRoomContract($item, $item);
        }

        DB::commit();
        $this->info('Done Patching '. $data->count() . ' room contracts name');
    }
}
