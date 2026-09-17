<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactSetting extends Model
{
    protected $fillable = [
        'admin_name',
        'admin_whatsapp_number',
    ];

    public static function singleton(): self
    {
        return self::query()->firstOrCreate(
            ['id' => 1],
            ['admin_name' => 'Admin MHC'],
        );
    }
}
