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
use function now;

final class EmailSend implements ShouldQueue
{
    use Batchable;
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;


    public function __construct(private readonly int $id, private readonly string $email, private readonly string $username)
    {
    }


    public function handle(): void
    {
        if ($this->batch()?->cancelled()) {
            return;
        }

        sleep(random_int(1, 10));

        Log::debug("{$this->username} ({$this->email}), your subscription is expiring soon");

        DB::table('users')->where('id', $this->id)->update(['notifiedts' => now()]);
    }


    public function middleware(): array
    {
        return [(new WithoutOverlapping($this->email))->dontRelease()];
    }
}
