<?php
return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header

        // Renderer settings
        'renderer' => [
            'template_path' => __DIR__ . '/../templates/',
        ],

        // Monolog settings
        'logger' => [
            'name' => 'slim-app',
            'path' => __DIR__ . '/../logs/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],

        // Random.org data
        'randomorg' => [
            'api_address' => 'https://api.random.org/json-rpc/2/invoke',
            'api_key' => getenv('RANDOM_ORG')
        ],
        
        // Wordnik data
        'wordnik' => [
            'api_key' => getenv('WORDNIK')
        ]
    ],
];
