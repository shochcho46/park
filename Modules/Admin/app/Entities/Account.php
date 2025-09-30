<?php

namespace Modules\Admin\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Account extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'category_id',
        'number_ticket',
        'ticket_price',
        'totalAmount',
        'type',
        'note'
    ];

    protected $casts = [
        'number_ticket' => 'integer',
        'ticket_price' => 'double',
        'totalAmount' => 'double',
        'type' => 'integer',
    ];

    // Type constants
    const TYPE_INCOME = 1;
    const TYPE_EXPENSE = 2;

    /**
     * Boot the model and define event listeners
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-calculate totalAmount only if both number_ticket and ticket_price are provided
        static::saving(function ($account) {
            // Only auto-calculate if both fields have values, otherwise keep manual totalAmount
            if (!empty($account->number_ticket) && !empty($account->ticket_price)) {
                $account->totalAmount = $account->number_ticket * $account->ticket_price;
            }
            // If totalAmount is not set and we don't have ticket data, default to 0
            if (empty($account->totalAmount) && (empty($account->number_ticket) || empty($account->ticket_price))) {
                $account->totalAmount = $account->totalAmount ?? 0;
            }
        });
    }

    /**
     * Relationship with Category
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get type as human readable text
     */
    public function getTypeTextAttribute()
    {
        return $this->type == self::TYPE_INCOME ? 'Income' : 'Expense/Maintenance';
    }

    /**
     * Get type badge class for UI
     */
    public function getTypeBadgeClassAttribute()
    {
        return $this->type == self::TYPE_INCOME ? 'bg-success' : 'bg-warning';
    }

    /**
     * Scope for income accounts
     */
    public function scopeIncome($query)
    {
        return $query->where('type', self::TYPE_INCOME);
    }

    /**
     * Scope for expense accounts
     */
    public function scopeExpense($query)
    {
        return $query->where('type', self::TYPE_EXPENSE);
    }

    /**
     * Scope for date range filtering
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        if ($startDate && $endDate) {
            return $query->whereBetween('created_at', [$startDate, $endDate]);
        }
        if ($startDate) {
            return $query->where('created_at', '>=', $startDate);
        }
        if ($endDate) {
            return $query->where('created_at', '<=', $endDate);
        }
        return $query;
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'id';
    }

    /**
     * Register media collections
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('account_docs')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/gif', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
            ->singleFile();
    }

    /**
     * Register media conversions
     */
    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(150)
            ->height(150)
            ->sharpen(10)
            ->performOnCollections('account_docs')
            ->nonQueued();
    }
}
