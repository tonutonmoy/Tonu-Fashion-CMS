<?php

namespace App\Models;


class FooterSetting extends BaseModel
{
    protected $fillable = [
        'logo',
        'description',
        'address',
        'phone',
        'email',
        'facebook_url',
        'instagram_url',
        'youtube_url',
        'whatsapp_number',
        'messenger_link',
        'copyright_text',
    ];
}
