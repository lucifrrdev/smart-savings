<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class SavingsGoal extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'target_amount',
        'current_amount',
        'target_date',
        'category',
        'color',
        'icon',
        'description',
        'status',
    ];

    protected $casts = [
        'target_amount' => 'decimal:2',
        'current_amount' => 'decimal:2',
        'target_date' => 'date',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function getProgressPercentageAttribute()
    {
        if ($this->target_amount <= 0) return 0;
        $percentage = ($this->current_amount / $this->target_amount) * 100;
        return min(100, round($percentage, 1));
    }

    public function getRemainingAmountAttribute()
    {
        return max(0, $this->target_amount - $this->current_amount);
    }

    public function getRemainingDaysAttribute()
    {
        $today = Carbon::today();
        $target = Carbon::parse($this->target_date);
        
        if ($target->isPast()) {
            return 0;
        }

        return $today->diffInDays($target);
    }

    public function getDailyTargetRecommendationAttribute()
    {
        $remaining = $this->remaining_amount;
        $days = $this->remaining_days;

        if ($remaining <= 0 || $days <= 0) {
            return 0;
        }

        return ceil($remaining / $days);
    }

    public function getMonthlyTargetRecommendationAttribute()
    {
        $remaining = $this->remaining_amount;
        $months = max(1, ceil($this->remaining_days / 30));

        if ($remaining <= 0) {
            return 0;
        }

        return ceil($remaining / $months);
    }
}
