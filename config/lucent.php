<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Lucent Authentication Secret Key
    |--------------------------------------------------------------------------
    |
    | Don't forget to set this in your .env file, as it will be used to authenticate
    | your request on Lucent Application]
    |
    */
    'lucent_key' => env('LUCENT_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Lucent Application URL
    |--------------------------------------------------------------------------
    |
    | Don't forget to set this in your .env file, as it is the url where
    | your LUcent Application is deployed
    |
    */
    'lucent_url' => env('LUCENT_URL'),

    /*
    |--------------------------------------------------------------------------
    | Request Detail
    |--------------------------------------------------------------------------
    |
    | You can set value True or False that you want to send request detail 
    | to Lucent or not
    |
    */
    'with_request_details' =>  true,
    /*
    |--------------------------------------------------------------------------
    | App Detail
    |--------------------------------------------------------------------------
    |
    | You can set value True or False that you want to send app detail 
    | to Lucent or not
    |
    */
    'with_app_details' => true,

    /*
    |--------------------------------------------------------------------------
    | User Detail
    |--------------------------------------------------------------------------
    |
    | You can set value True or False that you want to send user detail 
    | to Lucent or not
    |
    */
    'with_user_details' => true,

    /*
    |--------------------------------------------------------------------------
    | Line Count
    |--------------------------------------------------------------------------
    |
    | How many lines of the file on which exception is occured you want 
    | to see
    |
    */
    'line_count' => 40
];
