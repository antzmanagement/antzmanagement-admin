<?php

namespace App\Console\Commands;

use App\RentalPayment;
use Illuminate\Console\Command;
use App\Property;
use Illuminate\Support\Carbon;
use App\Traits\AllServices;
use Illuminate\Support\Facades\DB;

class UpdatePropertyList extends Command
{
    use AllServices;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:property_list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Property List';

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
        $properties = Property::all();

        $propertySet = collect([
            [
                    'name' => 'bed',
                    'text' => 'Bed',
                    'category' => 'furniture',
            ],
            [
                    'name' => 'heater',
                    'text' => 'Heater 1',
                    'category' => 'electric appliance',
            ],
            [
                    'name' => 'aircon',
                    'text' => 'Air Conditioner',
                    'category' => 'electric appliance',
            ],
            [
                    'name' => 'ceilingWall',
                    'text' => 'Ceiling Wall',
                    'category' => 'main structure',
            ],
            [
                    'name' => 'grillDoor',
                    'text' => 'Grill Door',
                    'category' => 'main structure',
            ],
        ]);

        foreach($properties as $property){
            $selectedProperty = $propertySet->where('name', $property->name)->first();
            if($selectedProperty){
                $selectedProperty = json_decode(json_encode($selectedProperty));
                $property->status = true;
                $property = $this->updateProperty($property, $selectedProperty);
                $propertySet = $propertySet->filter(function($item) use ($selectedProperty){
                    $item = json_decode(json_encode($item));
                    return $item->name != $selectedProperty->name;
                })->values();
            }else{
                $this->deleteProperty($property);
            }
            
        }

        if($propertySet->count() > 0){
            foreach($propertySet as $property){
                $property = json_decode(json_encode($property));
                $property = $this->createProperty($property);
            }
        }



        DB::commit();
        $this->info('Done Update Property List ');
    }
}
