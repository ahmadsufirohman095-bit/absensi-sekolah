<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

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
        'role',
        'custom_role', // Added custom_role field to fillable array
        'is_active',
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
     * Get the TU profile associated with the user.
     */
    public function tuProfile()
    {
        return $this->hasOne(TuProfile::class);
    }

    /**
     * Get the other profile associated with the user.
     */
    public function otherProfile()
    {
        return $this->hasOne(OtherProfile::class);
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
        } elseif ($this->role === 'tu' && $this->tuProfile) { // Tambahkan TU
            $fotoPath = $this->tuProfile->foto;
        } elseif ($this->role === 'other' && $this->otherProfile) { // Tambahkan Other
            $fotoPath = $this->otherProfile->foto;
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
            return in_array($this->role, $role) || ($this->role === 'other' && in_array($this->custom_role, $role));
        }
        return $this->role === $role || ($this->role === 'other' && $this->custom_role === $role);
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
     * Check if the user has the 'guru' role.
     *
     * @return bool
     */
    public function isGuru(): bool
    {
        return $this->hasRole('guru');
    }

    /**
     * Check if the user has the 'tu' role.
     *
     * @return bool
     */
    public function isTu(): bool
    {
        return $this->hasRole('tu');
    }

    /**
     * Check if the user has the 'other' role.
     *
     * @return bool
     */
    public function isOther(): bool
    {
        return $this->hasRole('other');
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
            case 'tu': // New role: TU
                return $this->tuProfile; // Menggunakan TuProfile
            case 'other': // New role: custom
                return $this->otherProfile; // Menggunakan OtherProfile
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
