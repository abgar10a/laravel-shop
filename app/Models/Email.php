<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;

class Email extends Model
{
    use HasFactory, Prunable;

    protected $fillable = ['user_id', 'subject', 'data', 'sent'];

    protected $casts = [
        'data' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function prunable()
    {
        return Email::query()->where('sent', '=', true);
    }
}
