<?php

namespace App\Console\Commands;

use App\RentalPayment;
use Illuminate\Console\Command;
use App\RoomContract;
use Illuminate\Support\Carbon;
use App\Traits\AllServices;
use Illuminate\Support\Facades\DB;

class CreateRental extends Command
{
    use AllServices;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:rental';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create the rental monthly';

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
        $roomContracts = RoomContract::where('status', true)->get();

        foreach ($roomContracts as $roomContract) {
            if ($roomContract->left > 0 ) {

                $room = $roomContract->room;
                if ($this->isEmpty($room)) {
                    DB::rollBack();
                    return $this->errorResponse();
                }
    
                if($roomContract->rental_type == 'month'){
                    $rentalDate = $this->toDate(Carbon::parse($roomContract->startdate)->addMonth($roomContract->latest)->startOfMonth());
                }else{
                    $rentalDate = $this->toDate(Carbon::parse($roomContract->startdate)->addDay($roomContract->latest)->startOfDay());
                }
                $rentalPayment = new RentalPayment();
                $rentalPayment->uid = Carbon::now()->timestamp . RentalPayment::count();
                $params = collect([
                    'price' => $roomContract->rental,
                    'rentaldate' => $rentalDate,
                    'remark' => '',
                    'room_contract_id' => $roomContract->id,
                ]);
                //Convert To Json Object
                $params = json_decode(json_encode($params));
                $rentalPayment = $this->createRentalPayment($params);
        
                if ($this->isEmpty($rentalPayment)) {
                    DB::rollBack();
                    return $this->errorResponse();
                }
        
                if (!$this->syncWithRentalPayment($roomContract)) {
                    DB::rollBack();
                    return $this->errorResponse();
                }
        
              
            }
        }

        DB::commit();
        $this->info('Done');
    }
}
