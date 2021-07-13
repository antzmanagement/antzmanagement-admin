<?php

namespace App\Console\Commands;

use App\Payment;
use Illuminate\Console\Command;
use App\Room;
use Illuminate\Support\Carbon;
use App\Traits\AllServices;
use Illuminate\Support\Facades\DB;

class DeletePaymentWithoutRoomContract extends Command
{
    use AllServices;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:payment_without_room_contract';

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
        $data = Payment::where('status', true)->get();

        $count = 0;

        foreach ($data as $item) {
            $roomcontract = $item->roomcontract;
            if(!$roomcontract->status){
                $count += 1;
                $this->deletePayment($item);
            }
        }

        DB::commit();
        $this->info('Done Deleting '. $count . ' payments without room contract');
    }
}
