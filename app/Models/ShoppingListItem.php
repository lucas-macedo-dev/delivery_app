<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShoppingListItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'shopping_list_id',
        'product_name',
        'quantity',
        'unit',
        'estimated_price',
        'actual_price',
        'purchased',
        'purchased_at',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'estimated_price' => 'decimal:2',
        'actual_price' => 'decimal:2',
        'purchased' => 'boolean',
        'purchased_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the shopping list that owns this item
     */
    public function shoppingList(): BelongsTo
    {
        return $this->belongsTo(ShoppingList::class);
    }
}
