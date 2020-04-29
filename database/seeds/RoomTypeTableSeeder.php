<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\RoomType;
use App\RoomTypeImage;
use App\RoomTypeService;
use App\RoomTypeProperty;
use Carbon\Carbon;

class RoomTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $imgs = [
            "https://res.cloudinary.com/ipoh-drum/image/upload/v1587403963/CP-B1112-2_olvbdv.jpg",
            "https://res.cloudinary.com/ipoh-drum/image/upload/v1587403962/maxresdefault_ra18v3.jpg",
            "https://res.cloudinary.com/ipoh-drum/image/upload/v1587278994/217486835_jw9ndq.jpg",
            "https://res.cloudinary.com/ipoh-drum/image/upload/v1587278992/badhostels_o9brym.jpg",
            "https://res.cloudinary.com/ipoh-drum/image/upload/v1587278991/Hostel_Dormitory_kp3ulz.jpg",
            "https://res.cloudinary.com/ipoh-drum/image/upload/v1587278990/182381214_kdfpz3.jpg",
            "https://res.cloudinary.com/ipoh-drum/image/upload/v1587998042/Hilton_Panama_Alienware_Room_Gaming_hotel_room_12_ynjtji.jpg",
            "https://res.cloudinary.com/ipoh-drum/image/upload/v1587998035/Screen_Shot_2019_10_08_at_2.29.55_PM.0_qy5nih.jpg",
            "https://res.cloudinary.com/ipoh-drum/image/upload/v1587998029/DesignbyEmilyHendersonDesignandPhotobySaraLigorria-Tramp_654-b8122ec9f66b4c69a068859958d8db37_v0evbc.jpg",
            "https://res.cloudinary.com/ipoh-drum/image/upload/v1587998024/download_k8zcpq.jpg",
            "https://res.cloudinary.com/ipoh-drum/image/upload/v1587998020/Guest-bedroom-refresh_zvdt8y.jpg",
            "https://res.cloudinary.com/ipoh-drum/image/upload/v1587998018/GettyImages-9261821821-5c69c1b7c9e77c0001675a49_hotr3e.jpg",
            "https://res.cloudinary.com/ipoh-drum/image/upload/v1587998015/213503505_y75krx.jpg"


        ];
        $faker = Faker::create();

        $roomType = new RoomType();
        $roomType->uid = Carbon::now()->timestamp . RoomType::count();
        $roomType->name = 'normal';
        $roomType->price = 210.00;
        $roomType->desc = $faker->paragraph();
        $roomType->save();

        for ($x = 0; $x < 5; $x++) {
            $image = new RoomTypeImage();
            $image->uid = Carbon::now()->timestamp . RoomTypeImage::count();
            $image->imgpath = $imgs[$faker->numberBetween($min = 0, $max = 12)];
            $image->roomType()->associate($roomType->id);
            $image->save();
        }
        
        $properties = RoomTypeProperty::where('status', true)->get();
        foreach ($properties as $property) {

            $roomType->properties()->attach($property->id);
        }

        $services = RoomTypeService::where('status', true)->get();
        foreach ($services as $service) {

            $roomType->services()->attach($service->id);
        }


        $roomType = new RoomType();
        $roomType->uid = Carbon::now()->timestamp . RoomType::count();
        $roomType->name = 'premium';
        $roomType->price = 290.00;
        $roomType->desc = $faker->paragraph();
        $roomType->save();

        for ($x = 0; $x < 6; $x++) {
            $image = new RoomTypeImage();
            $image->uid = Carbon::now()->timestamp . RoomTypeImage::count();
            $image->imgpath = $imgs[$faker->numberBetween($min = 0, $max = 12)];
            $image->roomType()->associate($roomType->id);
            $image->save();
        }

        $properties = RoomTypeProperty::where('status', true)->get();
        foreach ($properties as $property) {

            $roomType->properties()->attach($property->id);
        }

        $services = RoomTypeService::where('status', true)->get();
        foreach ($services as $service) {

            $roomType->services()->attach($service->id);
        }

        $roomType = new RoomType();
        $roomType->uid = Carbon::now()->timestamp . RoomType::count();
        $roomType->name = 'owner';
        $roomType->desc = $faker->paragraph();
        $roomType->price = 0.00;
        $roomType->save();

        for ($x = 0; $x < 7; $x++) {
            $image = new RoomTypeImage();
            $image->uid = Carbon::now()->timestamp . RoomTypeImage::count();
            $image->imgpath = $imgs[$faker->numberBetween($min = 0, $max = 12)];
            $image->roomType()->associate($roomType->id);
            $image->save();
        }

        
        $properties = RoomTypeProperty::where('status', true)->get();
        foreach ($properties as $property) {

            $roomType->properties()->attach($property->id);
        }

        $services = RoomTypeService::where('status', true)->get();
        foreach ($services as $service) {

            $roomType->services()->attach($service->id);
        }

    }
}
