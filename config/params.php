<?php

$default = [
    'adminEmail' => 'admin@example.com',
    'senderEmail' => 'noreply@example.com',
    'senderName' => 'Example.com mailer',
    'ymaps' => [
        'apiKey' => 'YOUR API KEY',
    ],
    'authClients' => [
        'vkontakte' => [
            'clientId' => 'YOUR ID KEY HERE',
            'clientSecret' => 'YOUR SECRET KEY',
        ],
    ],
];

$redeclared = require __DIR__ . DIRECTORY_SEPARATOR . 'params.local.php';
return array_merge($default, $redeclared);