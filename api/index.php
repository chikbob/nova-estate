<?php

declare(strict_types=1);

if (getenv('VERCEL')) {
    $storagePath = '/tmp/nova-estate-storage';
    foreach (['framework/cache/data', 'framework/sessions', 'framework/views', 'logs'] as $directory) {
        if (! is_dir($storagePath.'/'.$directory)) {
            mkdir($storagePath.'/'.$directory, 0775, true);
        }
    }

    $_ENV['LARAVEL_STORAGE_PATH'] = $storagePath;
    $_SERVER['LARAVEL_STORAGE_PATH'] = $storagePath;
}

require __DIR__.'/../public/index.php';
