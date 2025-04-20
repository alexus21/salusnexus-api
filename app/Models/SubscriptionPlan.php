<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'subscription_type',
        'price_monthly',
        'price_annual',
        'currency',
        'description',
        'is_active',
    ];

    public function features()
    {
        return $this->hasMany(SubscriptionFeature::class, 'subscription_type', 'subscription_type');
    }
}
