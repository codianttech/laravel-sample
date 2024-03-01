<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],
    'microsoftTeams' => [
        'notificationEnable' => env('ERRORSENDTOTEAMS', false),
        'webhookDsn' => env('ACTIVED_MS_TEAMS_DSN', null),
        'level' => env('LOG_LEVEL', 'debug'),
        'title' => '&#x1f41b Logging from ' . config('app.url'),
        'subject' => 'Error generated',
        'color' => '#fd0404',
        'type' => 'MessageCard',
        'context' => 'https://schema.org/extensions',
    ],
    'configCached' => env('CONFIG_CACHED', false)

];
