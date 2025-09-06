<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username', // Added username to fillable array
        'identifier', // Re-added identifier to fillable array        
        'email',
        'password',
        'role',    // Added role field to fillable array
        'is_active', // Added is_active field to fillable array
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
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'last_login_at' => 'datetime',
    ];

    // Remove the existing casts() method if present

    /**
     * Get the admin profile associated with the user.
     */
    public function adminProfile()
    {
        return $this->hasOne(AdminProfile::class);
    }

    /**
     * Get the teacher profile associated with the user.
     */
    public function guruProfile()
    {
        return $this->hasOne(GuruProfile::class);
    }

    /**
     * Get the student profile associated with the user.
     */
    public function siswaProfile()
    {
        return $this->hasOne(SiswaProfile::class);
    }

    /**
     * Get the attendance records associated with the user.
     */
    public function absensi()
    {
        return $this->hasMany(Absensi::class);
    }

    /**
     * Accessor untuk mendapatkan URL foto profil.
     * Akan memberikan URL default jika foto tidak ditemukan.
     *
     * @return string
     */
    public function getFotoUrlAttribute()
    {
        $fotoPath = null;

        // Cek profil berdasarkan role dan ambil path foto
        if ($this->role === 'admin' && $this->adminProfile) {
            $fotoPath = $this->adminProfile->foto;
        } elseif ($this->role === 'guru' && $this->guruProfile) {
            $fotoPath = $this->guruProfile->foto;
        } elseif ($this->role === 'siswa' && $this->siswaProfile) {
            $fotoPath = $this->siswaProfile->foto;
        }

        // Jika path foto ada dan file-nya benar-benar ada di storage
        if ($fotoPath && Storage::disk('public')->exists($fotoPath)) {
            return asset('storage/' . $fotoPath);
        }

        // Jika tidak ada foto, kembalikan URL avatar default berdasarkan nama
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=4f46e5&color=fff';
    }

    public function mataPelajarans()
    {
        return $this->belongsToMany(MataPelajaran::class, 'guru_mata_pelajaran', 'user_id', 'mata_pelajaran_id');
    }

    

    /**
     * Check if the user has the 'admin' role.
     *
     * @return bool
     */
    /**
     * Check if the user has a given role.
     *
     * @param string|array $role
     * @return bool
     */
    public function hasRole($role): bool
    {
        if (is_array($role)) {
            return in_array($this->role, $role);
        }
        return $this->role === $role;
    }

    /**
     * Check if the user has the 'admin' role.
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Get the correct profile relationship based on the user's role.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function getProfileAttribute()
    {
        switch ($this->role) {
            case 'admin':
                return $this->adminProfile;
            case 'guru':
                return $this->guruProfile;
            case 'siswa':
                return $this->siswaProfile;
            default:
                return null;
        }
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }
}
