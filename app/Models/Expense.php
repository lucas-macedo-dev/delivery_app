<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'description',
        'value',
        'expense_date',
        'user_inserter_id',
        'user_updater_id',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'expense_date' => 'date',
    ];

    /**
     * Get the user who created the expense
     */
    public function userInserter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_inserter_id');
    }

    /**
     * Get the user who last updated the expense
     */
    public function userUpdater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_updater_id');
    }

    /**
     * Scope to filter by description
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('description', 'like', '%' . $search . '%');
    }

    /**
     * Scope to filter by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('expense_date', [$startDate, $endDate]);
    }
}
