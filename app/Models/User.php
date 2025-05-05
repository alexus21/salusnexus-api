<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable {
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'date_of_birth',
        'gender',
        'dui',
        'phone',
        'email',
        'address',
        'latitude',
        'longitude',
        'address_reference',
        'password',
        'user_rol',
        'profile_photo_path',
        'active',
        'verified',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getUserInfoByItsId($id) {
        $user_rol = Auth::user()->user_rol;

        if ($user_rol == 'paciente') {
            return DB::table('users')
                ->leftJoin('patient_profiles', 'users.id', '=', 'patient_profiles.user_id')
                ->select(
                    'users.id AS user_id',
                    'users.first_name',
                    'users.last_name',
                    'users.date_of_birth',
                    'users.gender',
                    'users.dui',
                    'users.phone',
                    'users.address AS home_address',
                    'users.latitude',
                    'users.longitude',
                    'users.address_reference AS home_address_reference',
                    'users.email',
                    'users.user_rol',
                    'users.profile_photo_path',
                    'users.active',
                    'users.verified',
                )
                ->where('users.id', $id)
                ->first();
        }

        if ($user_rol == 'profesional') {
            return DB::table('users')
                ->join('professional_profiles', 'users.id', '=', 'professional_profiles.user_id')
                ->select(
                    'users.id AS user_id',
                    'users.first_name',
                    'users.last_name',
                    'users.date_of_birth',
                    'users.gender',
                    'users.dui',
                    'users.phone',
                    'users.address AS home_address',
                    'users.latitude',
                    'users.longitude',
                    'users.address_reference AS home_address_reference',
                    'users.email',
                    'users.user_rol',
                    'users.profile_photo_path',
                    'users.active',
                    'users.verified',
                )
                ->where('users.id', $id)
                ->first();
        }

        return null;
    }

    public function getUserProfile($id) {
        $user_rol = Auth::user()->user_rol;

        if ($user_rol == 'paciente') {
            return DB::table('users')
                ->leftJoin('patient_profiles', 'users.id', '=', 'patient_profiles.user_id')
                ->leftJoin('subscriptions', 'users.id', '=', 'subscriptions.user_id')
                ->leftJoin('payment_card_users', 'users.id', '=', 'payment_card_users.user_id')
                ->leftJoin('payment_cards', 'payment_card_users.payment_card_id', '=', 'payment_cards.id')
                ->select(
                    'users.id AS user_id',
                    'users.first_name',
                    'users.last_name',
                    'users.date_of_birth',
                    'users.gender',
                    'users.dui',
                    'users.phone',
                    'users.address AS home_address',
                    'users.latitude',
                    'users.longitude',
                    'users.address_reference AS home_address_reference',
                    'users.email',
                    'users.user_rol',
                    'users.profile_photo_path',
                    'users.active',
                    'users.verified',
                    'patient_profiles.id AS patient_profile_id',
                    'patient_profiles.emergency_contact_name',
                    'patient_profiles.emergency_contact_phone',
                    'patient_profiles.wants_health_tips',
                    'patient_profiles.wants_security_notifications',
                    'patient_profiles.wants_app_notifications',
                    'subscriptions.subscription_type',
                    'subscriptions.subscription_period',
                    DB::raw('COALESCE(TO_CHAR(subscriptions.end_date, \'DD/MM/YYYY\'), \'N/A\') AS end_date'),
                    DB::raw('COALESCE(RIGHT(payment_cards.card_number, 4), \'N/A\') AS card_number')
                )
                ->where('users.id', $id)
                ->first();
        }

        if ($user_rol == 'profesional') {
            return DB::table('users')
                ->join('professional_profiles', 'users.id', '=', 'professional_profiles.user_id')
                ->join('subscriptions', 'users.id', '=', 'subscriptions.user_id')
                ->leftJoin('payment_card_users', 'users.id', '=', 'payment_card_users.user_id')
                ->leftJoin('payment_cards', 'payment_card_users.payment_card_id', '=', 'payment_cards.id')
                ->select(
                    'users.id AS user_id',
                    'users.first_name',
                    'users.last_name',
                    'users.date_of_birth',
                    'users.gender',
                    'users.dui',
                    'users.phone',
                    'users.address AS home_address',
                    'users.latitude',
                    'users.longitude',
                    'users.address_reference AS home_address_reference',
                    'users.email',
                    'users.user_rol',
                    'users.profile_photo_path',
                    'users.active',
                    'users.verified',
                    'professional_profiles.id AS professional_profiles_id',
                    'professional_profiles.years_of_experience',
                    'professional_profiles.website_url',
                    'subscriptions.subscription_type',
                    'subscriptions.subscription_period',
                    DB::raw('COALESCE(TO_CHAR(subscriptions.end_date, \'DD/MM/YYYY\'), \'N/A\') AS end_date'),
                    DB::raw('COALESCE(RIGHT(payment_cards.card_number, 4), \'N/A\') AS card_number')
                )
                ->where('users.id', $id)
                ->first();
        }

        return null;
    }
}
