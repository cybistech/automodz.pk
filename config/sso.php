<?php

return [

    /*
    |--------------------------------------------------------------------------
    | SSO provider definitions
    |--------------------------------------------------------------------------
    |
    | `key` is used in URLs (/auth/{key}/callback). `driver` is the Socialite
    | driver name passed to Socialite::driver().
    |
    */

    'providers' => [
        'google' => [
            'label' => 'Gmail (Google)',
            'driver' => 'google',
            'button' => 'Continue with Google',
        ],
        'facebook' => [
            'label' => 'Facebook',
            'driver' => 'facebook',
            'button' => 'Continue with Facebook',
        ],
        'twitter' => [
            'label' => 'X (Twitter)',
            'driver' => 'twitter',
            'button' => 'Continue with X',
        ],
        'linkedin' => [
            'label' => 'LinkedIn',
            'driver' => 'linkedin-openid',
            'button' => 'Continue with LinkedIn',
        ],
        'instagram' => [
            'label' => 'Instagram',
            'driver' => 'instagram',
            'button' => 'Continue with Instagram',
        ],
    ],

];
