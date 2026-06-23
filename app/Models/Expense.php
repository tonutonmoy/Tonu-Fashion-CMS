<?php

namespace App\Models;

use App\Enums\ExpenseCategory;
use MongoDB\Laravel\Eloquent\Model as MongoModel;

class Expense extends MongoModel
{
    protected $connection = 'mongodb';

    protected $collection = 'expenses';

    protected $fillable = [
        'title',
        'category',
        'amount',
        'expense_date',
        'note',
        'admin_id',
    ];

    protected function casts(): array
    {
        return [
            'category' => ExpenseCategory::class,
            'amount' => 'float',
            'expense_date' => 'date',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function admin(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
