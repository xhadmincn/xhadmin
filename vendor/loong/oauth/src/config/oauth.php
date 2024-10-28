<?php

return [
    'certs' => config_path('certs'),
    'rsa_privatekey' => config_path('certs') . '/rsa_private.pem',
    'rsa_publickey' => config_path('certs') . '/rsa_public.pem',
    'prefix' => 'oauth',
    'expire' => 7200,
    'single' => false,
    'redis' => [
        'host' => getenv('REDIS_HOST'),
        'password' => getenv('REDIS_PASSWORD'),
        'port' => getenv('REDIS_PORT'),
        'select' => getenv('REDIS_DATABASE'),
    ],
];
