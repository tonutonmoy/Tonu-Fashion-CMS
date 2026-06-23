<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MongoCreateIndexesCommand extends Command
{
    protected $signature = 'mongo:create-indexes';

    protected $description = 'Create MongoDB indexes for inventory collections';

    public function handle(): int
    {
        if (config('database.default') !== 'mongodb') {
            $this->warn('Default connection is not mongodb — skipping index creation.');

            return self::SUCCESS;
        }

        $db = DB::connection('mongodb')->getMongoDB();

        $variants = $db->selectCollection('product_variants');
        $variants->updateMany(
            ['reserved_stock' => ['$exists' => false]],
            ['$set' => ['reserved_stock' => 0]]
        );
        $variants->createIndex(['product_id' => 1]);
        $variants->createIndex(['stock' => 1]);
        $variants->createIndex(['reserved_stock' => 1]);

        $products = $db->selectCollection('products');
        $products->updateMany(
            ['reserved_stock' => ['$exists' => false]],
            ['$set' => ['reserved_stock' => 0]]
        );
        $products->updateMany(
            ['purchase_price' => ['$exists' => false]],
            ['$set' => ['purchase_price' => 0]]
        );

        $movements = $db->selectCollection('stock_movements');
        $movements->createIndex(['product_variant_id' => 1, 'created_at' => -1]);
        $movements->createIndex(['order_id' => 1]);
        $movements->createIndex(['type' => 1, 'created_at' => -1]);
        $movements->createIndex(['admin_id' => 1]);

        $this->info('MongoDB inventory indexes created.');

        return self::SUCCESS;
    }
}
