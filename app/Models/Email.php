<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Email extends Model
{
    use HasFactory;

    protected $casts = [
        'checked' => 'boolean',
        'valid' => 'boolean',
    ];

    public $timestamps = false;
}
