<?php

$FM_ROUTE_PREFIX = "/filemanager/";
$FM_ROUTES =    ['ajax_calls.php' => ['get', 'post'],
                'dialog.php' => ['get'],
                'execute.php' => ['post'],
                'force_download.php' => ['post'],
                'fview.php' => ['get'],
                'upload.php' => ['get', 'post']];

require_once __DIR__.'/../src/Http/boot.php';

// Routes For Responsive API and Web (dialog.php)
Route::group(
    ['middleware' => 'web'],
    function () use ($FM_ROUTE_PREFIX, $FM_ROUTES) {
        foreach ($FM_ROUTES as $file => $method) {
            Route::match(
                $method,
                $FM_ROUTE_PREFIX.$file,
                function () use ($file) {
                    include __DIR__ . '/../Http/'.$file;
                    return ;
                }
            )->name('FM'.$file);
        }
    }
);

$FM_ROUTE_PREFIX = "/filemanager/";
$FM_ROUTES =    ['ajax_calls' => ['get', 'post'],
                'dialog' => ['get'],
                'execute' => ['post'],
                'force_download' => ['post'],
                'fview' => ['get'],
                'upload' => ['get', 'post']];

// require_once __DIR__.'/boot.php';

// Routes For Responsive API and Web (dialog.php)
Route::group(
    ['middleware' => 'web'],
    function () use ($FM_ROUTE_PREFIX, $FM_ROUTES) {
        foreach ($FM_ROUTES as $file => $method) {
            Route::match(
                $method,
                $FM_ROUTE_PREFIX.$file,
                function () use ($file) {
                    include __DIR__ . '/../src/Http/'.$file.'.php';
                    return ;
                }
            )->name('FM'.$file);
        }
    }
);
