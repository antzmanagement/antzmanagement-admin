<?php

namespace App\Console\Commands;

use App\RoomContract;
use Illuminate\Console\Command;
use App\Room;
use Illuminate\Support\Carbon;
use App\Traits\AllServices;
use Illuminate\Support\Facades\DB;

class PatchRoomContractDuration extends Command
{
    use AllServices;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'patch:room_contract_duration';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Patch Room Contract Duration';

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

            $contract = $this->getContractById($item->contract_id);
            if ($this->isEmpty($contract)) {
                DB::rollBack();
                return $this->notFoundResponse('Contract');
            }
            if(isset($item->rental_payment_start_date)){
                $rental_payment_start_date = Carbon::parse($item->rental_payment_start_date)->format('Y-m-d');
                if(Carbon::parse($startdate)->greaterThan(Carbon::parse($rental_payment_start_date))){
                    $rental_payment_start_date = $startdate;
                }else if(Carbon::parse($rental_payment_start_date)->greaterThan(Carbon::parse($enddate))){
                    $rental_payment_start_date = $enddate;
                }
            }else{
                $rental_payment_start_date = $startdate;
            }
    
            $duration = $contract->duration;
            if($contract->rental_type == 'day'){
                $duration = Carbon::parse($startdate)->diffInDays(Carbon::parse($enddate)) + 1;
            }else{
                $date1 = Carbon::parse($startdate)->firstOfMonth();
                $date2 = Carbon::parse($enddate)->firstOfMonth();
                if($date1->greaterThan($date2)){
                    $duration = 0;
                }else{
                    $duration = $date1->diffInMonths($date2, false) + 1;
                }
            }

            $item->startdate = $startdate;
            $item->enddate = $enddate;
            $item->rental_payment_start_date = $rental_payment_start_date;
            $item->duration = $duration;
            $this->updateRoomContract($item, $item);
            $this->syncWithRentalPayment($item);
        }

        DB::commit();
        $this->info('Done Patching '. $data->count() . ' room contracts duration');
    }
}
