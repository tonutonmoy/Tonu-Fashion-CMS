<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MongoCreateIndexesCommand extends Command
{
    protected $signature = 'mongo:create-indexes {--drop : Drop existing indexes before creating}';

    protected $description = 'Create MongoDB indexes for storefront performance';

    /** @var array<string, array<int, array{key: array<string, int>, options?: array<string, mixed>}>> */
    private array $definitions = [
        'products' => [
            ['key' => ['slug' => 1], 'options' => ['unique' => true, 'background' => true]],
            ['key' => ['status' => 1], 'options' => ['background' => true]],
            ['key' => ['featured' => 1], 'options' => ['background' => true]],
            ['key' => ['created_at' => -1], 'options' => ['background' => true]],
            ['key' => ['category_id' => 1], 'options' => ['background' => true]],
            ['key' => ['brand_id' => 1], 'options' => ['background' => true]],
            ['key' => ['effective_price' => 1], 'options' => ['background' => true]],
            ['key' => ['status' => 1, 'featured' => 1, 'created_at' => -1], 'options' => ['background' => true]],
            ['key' => ['status' => 1, 'category_id' => 1], 'options' => ['background' => true]],
            ['key' => ['status' => 1, 'brand_id' => 1], 'options' => ['background' => true]],
            ['key' => ['status' => 1, 'review_count' => -1], 'options' => ['background' => true]],
            ['key' => ['status' => 1, 'sale_price' => 1], 'options' => ['background' => true, 'sparse' => true]],
        ],
        'categories' => [
            ['key' => ['slug' => 1], 'options' => ['unique' => true, 'background' => true]],
            ['key' => ['status' => 1], 'options' => ['background' => true]],
            ['key' => ['sort_order' => 1], 'options' => ['background' => true]],
        ],
        'brands' => [
            ['key' => ['slug' => 1], 'options' => ['unique' => true, 'background' => true]],
            ['key' => ['status' => 1], 'options' => ['background' => true]],
        ],
        'orders' => [
            ['key' => ['order_number' => 1], 'options' => ['unique' => true, 'background' => true]],
            ['key' => ['status' => 1], 'options' => ['background' => true]],
            ['key' => ['customer_phone' => 1], 'options' => ['background' => true]],
            ['key' => ['customer_email' => 1], 'options' => ['background' => true, 'sparse' => true]],
            ['key' => ['created_at' => -1], 'options' => ['background' => true]],
        ],
        'users' => [
            ['key' => ['email' => 1], 'options' => ['unique' => true, 'background' => true, 'sparse' => true]],
            ['key' => ['phone' => 1], 'options' => ['background' => true, 'sparse' => true]],
            ['key' => ['status' => 1], 'options' => ['background' => true]],
        ],
        'posts' => [
            ['key' => ['slug' => 1], 'options' => ['unique' => true, 'background' => true]],
            ['key' => ['status' => 1], 'options' => ['background' => true]],
            ['key' => ['published_at' => -1], 'options' => ['background' => true]],
        ],
        'cms_pages' => [
            ['key' => ['slug' => 1], 'options' => ['unique' => true, 'background' => true]],
            ['key' => ['status' => 1], 'options' => ['background' => true]],
        ],
        'product_images' => [
            ['key' => ['product_id' => 1, 'is_primary' => 1], 'options' => ['background' => true]],
            ['key' => ['product_id' => 1, 'sort_order' => 1], 'options' => ['background' => true]],
        ],
        'reviews' => [
            ['key' => ['product_id' => 1, 'is_approved' => 1], 'options' => ['background' => true]],
            ['key' => ['is_approved' => 1, 'created_at' => -1], 'options' => ['background' => true]],
        ],
        'hero_slides' => [
            ['key' => ['status' => 1, 'sort_order' => 1], 'options' => ['background' => true]],
        ],
        'homepage_sections' => [
            ['key' => ['section_key' => 1], 'options' => ['unique' => true, 'background' => true]],
            ['key' => ['enabled' => 1, 'sort_order' => 1], 'options' => ['background' => true]],
        ],
        'newsletter_subscribers' => [
            ['key' => ['email' => 1], 'options' => ['unique' => true, 'background' => true]],
        ],
    ];

    public function handle(): int
    {
        $connection = DB::connection('mongodb');

        foreach ($this->definitions as $collection => $indexes) {
            $mongo = $connection->getCollection($collection);

            if ($this->option('drop')) {
                try {
                    $mongo->dropIndexes();
                    $this->warn("Dropped indexes on {$collection}");
                } catch (\Throwable $e) {
                    $this->warn("Could not drop indexes on {$collection}: {$e->getMessage()}");
                }
            }

            foreach ($indexes as $index) {
                $name = $mongo->createIndex($index['key'], $index['options'] ?? []);
                $fields = json_encode($index['key']);
                $this->line("  <info>{$collection}</info> → {$fields} ({$name})");
            }
        }

        $this->info('MongoDB indexes created.');

        return self::SUCCESS;
    }
}
