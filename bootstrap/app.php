<?php

declare(strict_types=1);

use App\Console\Kernel as ConsoleKernel;
use Illuminate\Contracts\Console\Kernel as ConsoleKernelContract;
use Illuminate\Contracts\Debug\ExceptionHandler as ExceptionHandlerContract;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

$app = new Application(
    $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__)
);

$app->singleton(
    ConsoleKernelContract::class,
    ConsoleKernel::class
);

$app->singleton(
    ExceptionHandlerContract::class,
    ExceptionHandler::class
);

return $app;
