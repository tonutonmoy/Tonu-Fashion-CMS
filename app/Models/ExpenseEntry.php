<?php

namespace App\Models;

use App\Enums\ExpenseCategory;

/** SQL fallback when default DB connection is not MongoDB. */
class ExpenseEntry extends BaseModel
{
    protected $table = 'expenses';

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
        ];
    }

    public function admin(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
