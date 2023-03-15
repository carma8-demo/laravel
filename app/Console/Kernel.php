<?php

declare(strict_types=1);

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

final class Kernel extends ConsoleKernel
{
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
    }


    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('emails:promote')->hourly()->withoutOverlapping();
        $schedule->command('emails:check')->hourly()->withoutOverlapping();
        $schedule->command('emails:send')->hourly()->withoutOverlapping();
    }
}
