<?php

namespace App\Models;


class Setting extends BaseModel
{
    protected $fillable = [
        'group',
        'key',
        'value',
        'type',
    ];
}
