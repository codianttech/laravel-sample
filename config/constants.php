<?php

return [
    /**
     * OTP will be sent on Email or phone number of User
     * Supported: "mail", "sms", "both",
     */
    'send_otp_by' => 'mail',

    /**
     * Reset Password will be sent on Email of the User
     * admin: "otp", "anything else" [anything else will trigger reset link mail]
     */
    'reset_password' => [
        'admin' => 'otp',
    ],

    'otp' => [
        'max_time' => 10, // time in minutes
        'otp_length' => 4, // time in minutes
        'is_default' => env('IS_DEFAULT_OTP', false),
        'default' => env('DEFAULT_OTP', 1111),
    ],
    'pagination_limit' => [
        'defaultPagination' => 10,
    ],

    /**
     * Single device login for mobile devices
     */
    'single_device_login' => env('SINGLE_DEVICE_LOGIN', true),

    'verification_required' => env('VERIFICATION_REQUIRED', false),

    'regex_validation' => [
        'strict_email' => '/^((?!.*?[_-]{2})[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-z0-9]+(\.[a-z0-9]+)*(\.[a-z]{2,3}))?$/',
        'email' => '/^([a-zA-Z0-9_\-\.\+]+)@([a-zA-Z0-9_\-\.]+)\.([a-zA-Z]{2,5})$/i',
        'password' => '/^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*[@$!%*?&])([a-zA-Z0-9@$!%*?&]{6,})$/',
        'multi_space' => '/  +/',
        'error_styling' => '/( [.*#(0-9)*]+)/m',
    ],

    /**
     * Date format to be used for the application
     */
    'date_format' => [
        'admin_display' => 'd-m-Y h:i A',
    ],

    /**
     * Profile image & logo configuration
     */
    'image' => [
        'defaultNoImage' => 'assets/images/no-image.png',
        'profile' => [
            'readAs' => 'public',
            'aspectRatio' => '1/1',
            'maxSize' => 1, // Add size in mb
            'dimension' => '150X150', // width X height
            'acceptType' => '.jpg,.jpeg,.png',
            'zoomAble' => true,
            'zoomOnWheel' => true,
            'cropBoxResizable' => false,
            'path' => 'profile_image',
        ],
        'logo' => [
            'readAs' => 'public',
            'aspectRatio' => '6/3',
            'maxSize' => 5, // Add size in mb
            'dimension' => '150X45', // width X height
            'acceptType' => '.jpg,.jpeg,.png',
            'zoomAble' => true,
            'zoomOnWheel' => true,
            'cropBoxResizable' => false,
            'path' => 'public/logo',
        ],
    ],
    'adminTabDisplay' => [
        // to make it hidden please add d-none
        'general' => '',
        'environment' => '',
    ],
];
