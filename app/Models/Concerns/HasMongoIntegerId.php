<?php

namespace App\Models\Concerns;

use App\Support\MongoSequence;

trait HasMongoIntegerId
{
    public static function bootHasMongoIntegerId(): void
    {
        static::creating(function (self $model) {
            if (empty($model->id)) {
                $model->id = MongoSequence::next($model->getTable());
            }
        });
    }
}
