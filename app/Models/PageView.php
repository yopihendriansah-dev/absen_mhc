<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class PageView extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'url',
        'session_id',
        'ip_address',
        'user_agent',
        'created_at',
        'event_id',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function scopeByUrl(Builder $query, string $url): Builder
    {
        return $query->where('url', $url);
    }

    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('created_at', now()->toDateString());
    }
}
