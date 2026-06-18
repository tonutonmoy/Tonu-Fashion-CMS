<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

class MongoSequence
{
    public static function next(string $collection): int
    {
        $result = DB::connection('mongodb')
            ->getCollection('counters')
            ->findOneAndUpdate(
                ['_id' => $collection],
                ['$inc' => ['seq' => 1]],
                ['upsert' => true, 'returnDocument' => \MongoDB\Operation\FindOneAndUpdate::RETURN_DOCUMENT_AFTER]
            );

        return (int) ($result['seq'] ?? 1);
    }

    public static function reset(string $collection, int $start = 0): void
    {
        DB::connection('mongodb')
            ->getCollection('counters')
            ->updateOne(
                ['_id' => $collection],
                ['$set' => ['seq' => $start]],
                ['upsert' => true]
            );
    }
}
