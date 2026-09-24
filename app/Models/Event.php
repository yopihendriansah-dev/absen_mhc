<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Event extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_CLOSED = 'closed';

    public const CAPACITY_LIMITED = 'limited';
    public const CAPACITY_UNLIMITED = 'unlimited';

    protected $fillable = [
        'name',
        'slug',
        'event_date',
        'start_time',
        'end_time',
        'description',
        'location_name',
        'location_address',
        'google_maps_url',
        'whatsapp_group_url',
        'capacity_type',
        'capacity',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
            'capacity' => 'integer',
        ];
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('event-posters')->singleFile();
        $this->addMediaCollection('event-og-images')->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumbnail')->width(800)->height(1000)->nonQueued();
    }

    public function hasAvailableCapacity(): bool
    {
        return $this->capacity_type === self::CAPACITY_UNLIMITED
            || $this->registrations()->where('status', 'registered')->count() < (int) $this->capacity;
    }

    public function isEnded(): bool
    {
        return $this->event_date?->isPast() && ! $this->event_date?->isToday();
    }

    public function isPubliclyVisible(): bool
    {
        return in_array($this->status, [self::STATUS_PUBLISHED, self::STATUS_CLOSED], true);
    }

    public function isRegistrationOpen(): bool
    {
        return $this->status === self::STATUS_PUBLISHED
            && ! $this->isEnded()
            && $this->hasAvailableCapacity();
    }
}
