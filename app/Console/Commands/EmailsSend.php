<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\EmailSend;
use Illuminate\Bus\Batch;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\Isolatable;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use stdClass;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use function now;
use function sprintf;

final class EmailsSend extends Command implements Isolatable
{
    private const CHUNK_SIZE = 1000;

    private const QUEUE = 'sends';

    protected $signature = 'emails:send';


    public function handle(): int
    {
        $users = DB::table('users')
            ->select('users.id', 'users.username', 'emails.email')
            ->join('emails', static function (JoinClause $join): void {
                $join->on('emails.user_id', 'users.id')->where('emails.valid', true);
            })
            ->where('users.validts', '<=', now()->addDays(3))
            ->where(static function (Builder $query): void {
                $query
                    ->whereNull('users.notifiedts')
                    ->orWhere('users.notifiedts', '<=', now()->subDays(3));
            })
            ->oldest('users.validts');

        Log::debug(sprintf('Found %s users, dispatching to %s queue by %d in a batch', $users->count(), self::QUEUE, self::CHUNK_SIZE));

        $users->chunk(
            self::CHUNK_SIZE,
            static fn (Collection $users): Batch => Bus::batch(
                $users->map(
                    static fn (stdClass $user): EmailSend => new EmailSend(
                        $user->id,
                        $user->email,
                        $user->username
                    )
                )
            )->onQueue(self::QUEUE)->dispatch()
        );

        Log::debug('Done');

        return SymfonyCommand::SUCCESS;
    }
}
