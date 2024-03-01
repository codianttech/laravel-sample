<?php

return [

    'enableSecretManager' => env('ENABLE_SECRET_MANAGER', false),
    'secretManagerProvider' => env('SECRET_MANAGER_PROVIDER', null),
    'checkSecretManagerApi' => env('CHECK_SECRET_MANAGER_API'),

    'aws' => [
        'region' => env('AWS_DEFAULT_REGION', ''),
        'secretName' => env('AWS_SECRET_NAME', ''),
        'sharedConfig' => env('AWS_SHARED_CONFIG', true),
    ],

    'configVariables' => [

        'LOG_CHANNEL' => 'logging.default',

        'DB_CONNECTION' => 'database.default',
        'DB_READ_HOST' => 'database.connections.mysql.read.host',
        'DB_HOST' => 'database.connections.mysql.write.host',
        'DB_PORT' => 'database.connections.mysql.port',
        'DB_DATABASE' => 'database.connections.mysql.database',
        'DB_USERNAME' => 'database.connections.mysql.username',
        'DB_PASSWORD' => 'database.connections.mysql.password',

        'BROADCAST_DRIVER' => 'broadcasting.default',
        'CACHE_DRIVER' => 'cache.default',
        'QUEUE_CONNECTION' => 'queue.default',
        'SESSION_DRIVER' => 'session.driver',
        'SESSION_LIFETIME' => 'session.lifetime',
        'SESSION_DOMAIN' => 'session.domain',
        'SESSION_SECURE_COOKIE' => 'session.secure',

        'REDIS_PORT' => 'database.redis.default.port',
        'REDIS_HOST' => 'database.redis.default.host',
        'REDIS_PASSWORD' => 'database.redis.default.password',
        'REDIS_USERNAME' => 'database.redis.default.username',

        'COOKIE_CONSENT_ENABLED' => 'cookie-consent.enabled',

        'DEFAULT_EMAIL' => 'constants.mail.default',

        'FILESYSTEM_DISK' => 'filesystems.default',
        'AWS_ACCESS_KEY_ID' => 'filesystems.disks.s3.key',
        'AWS_SECRET_ACCESS_KEY' => 'filesystems.disks.s3.secret',
        'AWS_DEFAULT_REGION' => 'filesystems.disks.s3.region',
        'AWS_BUCKET' => 'filesystems.disks.s3.bucket',
        'AWS_URL' => 'filesystems.disks.s3.url',

        'AWS_PRIVATE_BUCKET' => 'filesystems.disks.private.bucket',
        'AWS_PRIVATE_URL' => 'filesystems.disks.private.url',

        'MAIL_MAILER' => 'mail.mailers.default',
        'MAIL_HOST' => 'mail.mailers.smtp.host',
        'MAIL_PORT' => 'mail.mailers.smtp.port',
        'MAIL_USERNAME' => 'mail.mailers.smtp.username',
        'MAIL_PASSWORD' => 'mail.mailers.smtp.password',
        'MAIL_ENCRYPTION' => 'mail.mailers.smtp.encryption',
        'MAIL_FROM_ADDRESS' => 'mail.from.address',

    ],

];
