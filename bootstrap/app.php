<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\PegawaiMiddleware;
use Maatwebsite\Excel\ExcelServiceProvider;
use Maatwebsite\Excel\Facades\Excel;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withProviders([
        ExcelServiceProvider::class,
    ])
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'admin' => AdminMiddleware::class,
            'pegawai' => PegawaiMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();
