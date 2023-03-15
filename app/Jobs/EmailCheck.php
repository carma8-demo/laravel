<?php

declare(strict_types=1);

namespace App\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

final class EmailCheck implements ShouldQueue
{
    use Batchable;
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;


    public function __construct(private readonly string $email)
    {
    }


    public function handle(): void
    {
        if ($this->batch()?->cancelled()) {
            return;
        }

        sleep(random_int(1, 60));

        $value = (bool) random_int(0, 1);

        Log::debug("Check {$this->email}: {$value}");

        DB::table('emails')->where('email', $this->email)->update(['checked' => true, 'valid' => $value]);
    }


    public function middleware(): array
    {
        return [(new WithoutOverlapping($this->email))->dontRelease()];
    }
}
