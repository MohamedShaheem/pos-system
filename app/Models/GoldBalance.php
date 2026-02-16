<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class GoldBalance extends Model
{
    use HasFactory;

    protected $fillable = [
        'description', 
        'gold_in',  
        'gold_out',  
        'gold_balance',  
    ];

    protected $casts = [
        'gold_in' => 'decimal:3',
        'gold_out' => 'decimal:3',
        'gold_balance' => 'decimal:3',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the formatted gold_in value
     *
     * @return string
     */
    public function getFormattedGoldInAttribute(): string
    {
        return $this->gold_in ? number_format($this->gold_in, 3) : '0.000';
    }

    /**
     * Get the formatted gold_out value
     *
     * @return string
     */
    public function getFormattedGoldOutAttribute(): string
    {
        return $this->gold_out ? number_format($this->gold_out, 3) : '0.000';
    }

    /**
     * Get the formatted gold_balance value
     *
     * @return string
     */
    public function getFormattedGoldBalanceAttribute(): string
    {
        return number_format($this->gold_balance, 3);
    }

    /**
     * Get the transaction type
     *
     * @return string
     */
    public function getTransactionTypeAttribute(): string
    {
        if ($this->gold_in > 0 && $this->gold_out > 0) {
            return 'both';
        } elseif ($this->gold_in > 0) {
            return 'in';
        } elseif ($this->gold_out > 0) {
            return 'out';
        } else {
            return 'opening';
        }
    }

    /**
     * Get the net amount for this transaction
     *
     * @return float
     */
    public function getNetAmountAttribute(): float
    {
        return ($this->gold_in ?? 0) - ($this->gold_out ?? 0);
    }

    /**
     * Scope to get transactions within date range
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $startDate
     * @param string $endDate
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithinDateRange($query, string $startDate, string $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope to get only incoming transactions
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIncoming($query)
    {
        return $query->whereNotNull('gold_in')->where('gold_in', '>', 0);
    }

    /**
     * Scope to get only outgoing transactions
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOutgoing($query)
    {
        return $query->whereNotNull('gold_out')->where('gold_out', '>', 0);
    }

    /**
     * Scope to order by creation date
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $direction
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrderByDate($query, string $direction = 'asc')
    {
        return $query->orderBy('created_at', $direction);
    }

    /**
     * Get the previous entry
     *
     * @return self|null
     */
    public function getPreviousEntry(): ?self
    {
        return static::where('created_at', '<', $this->created_at)
            ->orderBy('created_at', 'desc')
            ->first();
    }

    /**
     * Get the next entry
     *
     * @return self|null
     */
    public function getNextEntry(): ?self
    {
        return static::where('created_at', '>', $this->created_at)
            ->orderBy('created_at', 'asc')
            ->first();
    }

    /**
     * Boot method to handle model events
     */
   protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Allow 0, only throw if BOTH are null
            if (is_null($model->gold_in) && is_null($model->gold_out)) {
                throw new \Exception('Either gold_in or gold_out must be specified');
            }
        });
    }

}