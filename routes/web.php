<?php

use Boot\Support\Facades\Route;
use Boot\Support\Facades\Config;

require_once __DIR__.'/../src/Http/boot.php';

$prefix = Config::get('responsivefilemanager.route_prefix')."/";
$routes =    ['ajax_calls' => ['get', 'post'],
                'dialog' => ['get'],
                'execute' => ['post'],
                'force_download' => ['post'],
                'fview' => ['get'],
                'upload' => ['get', 'post']];

Route::group(
    ['middleware' => Config::get('responsivefilemanager.middleware')],
    function () use ($prefix, $routes) {
        foreach ($routes as $file => $method) {
            Route::match(
                $method,
                $prefix.$file,
                function () use ($file) {
                    include __DIR__ . '/../src/Http/'.$file.'.php';
                    return;
                }
            )->name('FM'.$file);
        }
    }
);
