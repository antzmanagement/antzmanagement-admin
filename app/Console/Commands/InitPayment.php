<?php

namespace App\Console\Commands;

use App\RentalPayment;
use App\Payment;
use Illuminate\Console\Command;
use App\RoomContract;
use Illuminate\Support\Carbon;
use App\Traits\AllServices;
use Illuminate\Support\Facades\DB;

class InitPayment extends Command
{
    use AllServices;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'init:payment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'init the payment';

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
        $rentalPayments = RentalPayment::where('status', true)->get();

        foreach ($rentalPayments as $rentalPayment) {
            if($rentalPayment->paid){
                $rentalPayment->payment = $rentalPayment->price + $rentalPayment->penalty + $rentalPayment->processing_fees;
                $rentalPayment->outstanding = 0;
            }else{
                $rentalPayment->payment = 0;
                $rentalPayment->outstanding = $rentalPayment->price + $rentalPayment->penalty + $rentalPayment->processing_fees;
            }
            $this->updateRentalPayment($rentalPayment, $rentalPayment);
        }

        $payments = Payment::where('status', true)->get();

        foreach ($payments as $payment) {
            if($payment->paid){
                $payment->totalpayment = $payment->price + $payment->other_charges + $payment->processing_fees;
                $payment->outstanding = 0;
            }else{
                $payment->totalpayment = 0;
                $payment->outstanding = $payment->price + $payment->other_charges + $payment->processing_fees;
            }
            $this->updatePayment($payment, $payment);
        }

        DB::commit();
        $this->info('Done');
    }
}
