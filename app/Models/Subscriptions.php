<?php

namespace App\Models;

use Database\Factories\SubscriptionsFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class Subscriptions extends Model
{
    /** @use HasFactory<SubscriptionsFactory> */
    use HasFactory, HasApiTokens, Notifiable;

    protected $table = 'subscriptions';

    protected $fillable = [
        'user_id',
        'subscription_type',
        'subscription_status',
        'type_subscription_plan',
        'start_date',
        'end_date',
        'trial_ends_at',
        'auto_renew',
        'payment_provider_subscription_id',
        'created_at',
        'updated_at'
    ];
}
