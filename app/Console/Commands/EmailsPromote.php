<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\Isolatable;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use function sprintf;

final class EmailsPromote extends Command implements Isolatable
{
    protected $signature = 'emails:promote';


    public function handle(): int
    {
        $count = DB::table('emails')
            ->join('users', static function (JoinClause $join): void {
                $join->on('users.id', 'emails.user_id')->where('users.confirmed', true);
            })
            ->where('emails.valid', false)
            ->update(['emails.checked' => true, 'emails.valid' => true]);

        Log::debug(sprintf('Found %d confirmed emails and promoted as checked and validated', $count));

        return SymfonyCommand::SUCCESS;
    }
}
