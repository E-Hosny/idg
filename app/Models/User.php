<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'notification_email',
        'locale',
        'password',
        'role',
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

    public function isReceptionist(): bool
    {
        return $this->role === 'receptionist';
    }

    public function isLab(): bool
    {
        return $this->role === 'lab';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function receivesWorkflowNotifications(): bool
    {
        return in_array($this->role, ['receptionist', 'lab', 'admin'], true);
    }

    /**
     * Email used for workflow notification mail channel (falls back to login email).
     */
    public function routeNotificationForMail(): string
    {
        return $this->notification_email ?: $this->email;
    }

    public function hasDeliverableNotificationEmail(): bool
    {
        return filter_var($this->routeNotificationForMail(), FILTER_VALIDATE_EMAIL) !== false;
    }

    public function effectiveNotificationEmail(): string
    {
        return $this->routeNotificationForMail();
    }

    public function usesLoginEmailForNotifications(): bool
    {
        return blank($this->notification_email);
    }

    public function preferredLocale(): string
    {
        return in_array($this->locale, ['ar', 'en'], true) ? $this->locale : 'en';
    }
}
