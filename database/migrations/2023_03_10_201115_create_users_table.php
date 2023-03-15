<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', static function (Blueprint $table): void {
            $table->id();
            $table->string('username')->unique();
            $table->timestamp('notifiedts')->nullable()->index();
            $table->timestamp('validts')->index();
            $table->boolean('confirmed')->default(false)->index();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
