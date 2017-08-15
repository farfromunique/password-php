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
            'api_address' => 'https://api.random.org/json-rpc/1/invoke',
            'api_key' => '3eebc5f0-0b62-422f-9249-dc94ad648cc0'
		]
    ],
];
