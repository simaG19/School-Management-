<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, HasPanelShield;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
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
    public function canAccessPanel(\Filament\Panel $panel): bool
    {
        return $this->hasRole('admin') || $this->hasRole('teacher') || $this->hasRole('student');
    }



    // Automatically hash passwords
public function setPasswordAttribute($value)
{
    if ($value) {
        $this->attributes['password'] = bcrypt($value);
    }
}

   // A teacher can have many grades assigned to them.
   public function assignedGrades()
   {
       return $this->hasMany(Grade::class, 'teacher_id');
   }

   // A student can have many grades received.
   public function receivedGrades()
   {
       return $this->hasMany(Grade::class, 'student_id');
   }

   // A user can have many messages.
   public function messages()
   {
       return $this->hasMany(Message::class);
   }


}
