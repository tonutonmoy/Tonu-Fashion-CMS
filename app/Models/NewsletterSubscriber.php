<?php

namespace App\Models;


class NewsletterSubscriber extends BaseModel
{
    protected $fillable = [
        'email',
        'status',
    ];
}
