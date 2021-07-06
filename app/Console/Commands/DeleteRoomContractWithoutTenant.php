<?php

namespace App\Console\Commands;

use App\RoomContract;
use Illuminate\Console\Command;
use App\Room;
use Illuminate\Support\Carbon;
use App\Traits\AllServices;
use Illuminate\Support\Facades\DB;

class DeleteRoomContractWithoutTenant extends Command
{
    use AllServices;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:room_contract_without_tenant';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete Room Contract Without Tenant';

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

        $count = 0;

        foreach ($data as $item) {
            $tenant = $item->tenant;
            if(!$tenant->status){
                $count += 1;
                $this->deleteRoomContract($item);
            }
        }

        DB::commit();
        $this->info('Done Deleting '. $count . ' room contracts without tenant');
    }
}
