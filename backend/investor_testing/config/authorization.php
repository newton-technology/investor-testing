<?php

use Newton\InvestorTesting\Packages\Authorization\GrantCodeService;
use Newton\InvestorTesting\Packages\Authorization\GrantPasswordService;
use Newton\InvestorTesting\Packages\Authorization\GrantRefreshService;

$issuers = [];
if ($authorizationServerEnabled = env('AUTHORIZATION_SERVER_ENABLED', true)) {
    $issuers['newton-technology/investor_testing'] = [
        'aud' => [
            '*',
        ],
        'enabled' => env('AUTHORIZATION_SERVER_ENABLED', false),
        'sslCert' => env('AUTHORIZATION_SERVER_PUBLIC_KEY', base_path('public.pem')),
    ];
}

return [
    'jwt_leeway' => env('JWT_LEEWAY', 60),
    'issuer' => $issuers,
    'server' => [
        'enabled' => $authorizationServerEnabled,
        'grants' => [
            'code' => [
                'service' => GrantCodeService::class,
                'properties' => [
                    'enabled' => env('AUTHORIZATION_SERVER_GRANT_CODE_ENABLED', true),
                    'code' => [
                        'length' => env('AUTHORIZATION_SERVER_GRANT_CODE_LENGTH', 6),
                        'lifetime' => env('AUTHORIZATION_SERVER_GRANT_CODE_LIFETIME', 600),
                        'attempts_max' => env('AUTHORIZATION_SERVER_GRANT_CODE_ATTEMPTS_MAX', 5),
                    ],
                ],
            ],
            'password' => [
                'service' => GrantPasswordService::class,
                'properties' => [
                    'enabled' => env('AUTHORIZATION_SERVER_GRANT_PASSWORD_ENABLED', true),
                ],
            ],
            'refresh_token' => [
                'service' => GrantRefreshService::class,
                'properties' => [
                    'enabled' => env('AUTHORIZATION_SERVER_GRANT_REFRESH_ENABLED', true),
                ],
            ],
        ],
        'issuer' => 'newton-technology/investor_testing',
        'privateKey' => env('AUTHORIZATION_SERVER_PRIVATE_KEY', base_path('private.pem')),
        'publicKey' => env('AUTHORIZATION_SERVER_PUBLIC_KEY', base_path('public.pem')),
        'token' => [
            'algorithm' => env('AUTHORIZATION_SERVER_TOKEN_ALGORITHM', 'RS256'),
            'access' => [
                'lifetime' => env('AUTHORIZATION_SERVER_TOKEN_ACCESS_LIFETIME', 600),
            ],
            'refresh' => [
                'lifetime' => env('AUTHORIZATION_SERVER_TOKEN_REFRESH_LIFETIME', 600),
            ],
        ],
    ],
];
