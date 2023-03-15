<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Email;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;
use Illuminate\Support\Benchmark;
use Illuminate\Support\Facades\DB;

final class DatabaseSeeder extends Seeder
{
    public function run(): never
    {
        Benchmark::dd(static function (): void {
            DB::transaction(static function (): void {
                $iterations = 10;
                $count = 100_000;
                $size = 8_000;

                for ($i = 0; $i < $iterations; $i++) {
                    $chunks = User::factory()->count($count)->make()->chunk($size);
                    foreach ($chunks as $chunk) {
                        DB::table('users')->insert($chunk->toArray());
                    }

                    $chunks = Email::factory()->count($count)->sequence(static fn (Sequence $sequence): array => ['user_id' => $i * $count + $sequence->index + 1])->make()->chunk($size);
                    foreach ($chunks as $chunk) {
                        DB::table('emails')->insert($chunk->toArray());
                    }
                }
            });
        });
    }
}
