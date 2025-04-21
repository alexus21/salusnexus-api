<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SubscriptionPlan extends Model {
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

    public function features() {
        return $this->hasMany(SubscriptionFeature::class, 'subscription_type', 'subscription_type');
    }

    // Methods
    public static function getPlanByType(string $plan_type): Collection {
        return DB::table('subscription_features')
            ->join('subscription_plans', 'subscription_plans.subscription_type', '=', 'subscription_features.subscription_type')
            ->where('subscription_plans.subscription_type', $plan_type)
            ->where('subscription_plans.is_active', true)
            ->select(
                'subscription_plans.id as subscription_plan_id',
                'subscription_plans.price_monthly',
                'subscription_plans.price_annual',
                'subscription_plans.currency',
                'subscription_plans.description',
                'subscription_plans.is_active',
                'subscription_features.id as subscription_feature_id',
                'subscription_features.feature'
            )
            ->get();

    }
}
