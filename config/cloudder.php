<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cloudinary API configuration
    |--------------------------------------------------------------------------
    |
    | Before using Cloudinary you need to register and get some detail
    | to fill in below, please visit cloudinary.com.
    |
    */

    'cloudName'  => env('CLOUDINARY_CLOUD_NAME', 'dwslzbgaa'),
    'baseUrl'    => env('CLOUDINARY_BASE_URL', 'http://res.cloudinary.com/'.env('CLOUDINARY_CLOUD_NAME')),
    'secureUrl'  => env('CLOUDINARY_SECURE_URL', 'https://res.cloudinary.com/'.env('CLOUDINARY_CLOUD_NAME')),
    'apiBaseUrl' => env('CLOUDINARY_API_BASE_URL', 'https://api.cloudinary.com/v1_1/'.env('CLOUDINARY_CLOUD_NAME')),
    'apiKey'     => env('CLOUDINARY_API_KEY', '914426785316647'),
    'apiSecret'  => env('CLOUDINARY_API_SECRET', 'sA0zIvXlhnzWqI_XC2A8NFbCLNI'),

    'scaling'    => [
        'format' => 'png',
        'width'  => 150,
        'height' => 150,
        'crop'   => 'fit',
        'effect' => null
    ],

];
