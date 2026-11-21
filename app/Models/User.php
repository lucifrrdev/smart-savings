<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'class',
        'nisn',
        'nip',
        'birth_date',
        'role',
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
            'password' => 'hashed',
            'birth_date' => 'date',
        ];
    }

    public function savingsGoals()
    {
        return $this->hasMany(SavingsGoal::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Dapatkan digit/nomor kelas yang diampu (misal: 'Wali Kelas 4' -> '4').
     */
    public function getAssignedClassGrade(): ?string
    {
        if (preg_match('/(\d+)/', $this->class ?? '', $matches)) {
            return $matches[1];
        }
        return null;
    }

    /**
     * Dapatkan label kelas yang diampu (misal: 'Kelas 4').
     */
    public function getAssignedClassLabelAttribute(): string
    {
        $grade = $this->getAssignedClassGrade();
        if ($grade) {
            return 'Kelas ' . $grade;
        }

        if (!empty($this->class)) {
            return trim(preg_replace('/^wali\s*/i', '', $this->class));
        }

        return 'Kelas';
    }
}
