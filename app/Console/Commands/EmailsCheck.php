<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\EmailCheck;
use Illuminate\Bus\Batch;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\Isolatable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use stdClass;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use function sprintf;

final class EmailsCheck extends Command implements Isolatable
{
    private const CHUNK_SIZE = 1000;

    private const QUEUE = 'checks';

    protected $signature = 'emails:check';


    public function handle(): int
    {
        $emails = DB::table('emails')->select('email')->where('checked', false)->orderBy('email');

        Log::debug(sprintf('Found %s emails, dispatching to %s queue by %d in a batch', $emails->count(), self::QUEUE, self::CHUNK_SIZE));

        $emails->chunk(
            self::CHUNK_SIZE,
            static fn (Collection $emails): Batch => Bus::batch(
                $emails->map(
                    static fn (stdClass $email): EmailCheck => new EmailCheck($email->email)
                )
            )->onQueue(self::QUEUE)->dispatch()
        );

        Log::debug('Done');

        return SymfonyCommand::SUCCESS;
    }
}
