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
                    'name' => 'studyTable',
                    'text' => 'Study Table',
                    'category' => 'furniture',
            ],
            [
                    'name' => 'bed',
                    'text' => 'Bed Frame',
                    'category' => 'furniture',
            ],
            [
                    'name' => 'mattress',
                    'text' => 'Mattress',
                    'category' => 'furniture',
            ],
            [
                    'name' => 'wardrobe',
                    'text' => 'Wardrobe',
                    'category' => 'furniture',
            ],
            [
                    'name' => 'sideTable',
                    'text' => 'Side Table',
                    'category' => 'furniture',
            ],
            [
                    'name' => 'others',
                    'text' => 'Others',
                    'category' => 'furniture',
            ],
            [
                    'name' => 'light',
                    'text' => 'Light',
                    'category' => 'electric_appliances',
            ],
            [
                    'name' => 'lightBulb',
                    'text' => 'Light Bulb',
                    'category' => 'electric_appliances',
            ],
            [
                    'name' => 'ceilingFan',
                    'text' => 'Ceiling Fan',
                    'category' => 'electric_appliances',
            ],
            [
                    'name' => 'wallFan',
                    'text' => 'Wall Fan',
                    'category' => 'electric_appliances',
            ],
            [
                    'name' => 'standFan',
                    'text' => 'Stand Fan',
                    'category' => 'electric_appliances',
            ],
            [
                    'name' => 'washingMachine',
                    'text' => 'Washing Machine',
                    'category' => 'electric_appliances',
            ],
            [
                    'name' => 'aircon',
                    'text' => 'Air Conditioner',
                    'category' => 'electric_appliances',
            ],
            [
                    'name' => 'waterHeater',
                    'text' => 'Water heater',
                    'category' => 'electric_appliances',
            ],
            [
                    'name' => 'others',
                    'text' => 'Others',
                    'category' => 'electric_appliances',
            ],
            [
                    'name' => 'sink',
                    'text' => 'Sink',
                    'category' => 'wash_room',
            ],
            [
                    'name' => 'toiletBowl',
                    'text' => 'Toilet bowl',
                    'category' => 'wash_room',
            ],
            [
                    'name' => 'waterTap',
                    'text' => 'Water Tap',
                    'category' => 'wash_room',
            ],
            [
                    'name' => 'ceiling',
                    'text' => 'Ceiling',
                    'category' => 'main_structure',
            ],
            [
                    'name' => 'electricalDbBox',
                    'text' => 'Electrical Db Box',
                    'category' => 'main_structure',
            ],
            [
                    'name' => 'electricalWiring',
                    'text' => 'Electrical Wiring',
                    'category' => 'main_structure',
            ],
            [
                    'name' => 'tnbMeter',
                    'text' => 'TNB Meter',
                    'category' => 'main_structure',
            ],
            [
                    'name' => 'waterMeter',
                    'text' => 'Water Meter',
                    'category' => 'main_structure',
            ],
            [
                    'name' => 'plumbing',
                    'text' => 'Plumbing',
                    'category' => 'main_structure',
            ],
            [
                    'name' => 'flooring',
                    'text' => 'Flooring',
                    'category' => 'main_structure',
            ],
            [
                    'name' => 'flooring',
                    'text' => 'Flooring',
                    'category' => 'main_structure',
            ],
            [
                    'name' => 'wallPaper',
                    'text' => 'Wall Paper',
                    'category' => 'main_structure',
            ],
            [
                    'name' => 'painting',
                    'text' => 'Painting',
                    'category' => 'main_structure',
            ],
            [
                    'name' => 'others',
                    'text' => 'Others',
                    'category' => 'main_structure',
            ],
        ]);

        foreach($properties as $property){
            $selectedProperty = $propertySet->where('name', $property->name)->where('category', $property->category)->first();
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
