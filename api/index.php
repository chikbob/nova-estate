<?php

declare(strict_types=1);

if (getenv('VERCEL')) {
    $storagePath = '/tmp/nova-estate-storage';
    foreach (['bootstrap/cache', 'framework/cache/data', 'framework/sessions', 'framework/views', 'logs'] as $directory) {
        if (! is_dir($storagePath.'/'.$directory)) {
            mkdir($storagePath.'/'.$directory, 0775, true);
        }
    }

    $_ENV['LARAVEL_STORAGE_PATH'] = $storagePath;
    $_SERVER['LARAVEL_STORAGE_PATH'] = $storagePath;

    foreach ([
        'APP_CONFIG_CACHE' => $storagePath.'/bootstrap/cache/config.php',
        'APP_EVENTS_CACHE' => $storagePath.'/bootstrap/cache/events.php',
        'APP_PACKAGES_CACHE' => $storagePath.'/bootstrap/cache/packages.php',
        'APP_ROUTES_CACHE' => $storagePath.'/bootstrap/cache/routes.php',
        'APP_SERVICES_CACHE' => $storagePath.'/bootstrap/cache/services.php',
        'VIEW_COMPILED_PATH' => $storagePath.'/framework/views',
    ] as $key => $value) {
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }
}

require __DIR__.'/../public/index.php';
