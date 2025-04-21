<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionFeature extends Model {
    use HasFactory;

    protected $fillable = [
        'feature',
        'subscription_type',
    ];

    public function plan() {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_type', 'subscription_type');
    }
}
