<?php

namespace App\Models;

use App\Models\Concerns\HasMongoIntegerId;
use MongoDB\Laravel\Eloquent\Model;

abstract class BaseModel extends Model
{
    use HasMongoIntegerId;

    protected $connection = 'mongodb';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'int';
}
