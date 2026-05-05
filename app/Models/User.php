<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_photo',
        'email_notifications',
        'sms_notifications',
        'marketing_emails',
        'language',
        'currency',
        'phone',
        'date_of_birth',
        'role',
        'address',
        'city',
        'pincode',
        'user_code',
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
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function consultations()
    {
        return $this->hasMany(Consultation::class);
    }

    /**
     * Boot method to auto-generate user_code on creation.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (empty($user->user_code)) {
                $user->user_code = static::generateUserCode($user->role ?? 'user');
            }
        });
    }

    /**
     * Generate a unique user code.
     * Format: RoleLetter(1) + Day(2) + MonthLetter(1) + Random(4) = 8 chars
     */
    public static function generateUserCode(?string $role = 'user'): string
    {
        $monthLetters = ['J','F','M','A','Y','N','L','G','S','O','V','D']; // unique per month

        do {
            $roleLetter = strtoupper(substr($role ?? 'user', 0, 1)); // U, A, or E
            $day = now()->format('d'); // 2-digit day
            $monthIndex = (int) now()->format('n') - 1;
            $monthLetter = $monthLetters[$monthIndex];
            $random = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 4));
            $code = $roleLetter . $day . $monthLetter . $random;
        } while (static::where('user_code', $code)->exists());

        return $code;
    }

    public function kundlis()
    {
        return $this->hasMany(Kundli::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function poojas()
    {
        return $this->hasMany(Pooja::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }
}
