<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Category extends Model
{
    use HasFactory;

    // Arahkan ke tabel custom
    protected $table = 'tblm_kategori';

    protected $fillable = [
        'nama',
        'keterangan',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'status'     => 'boolean', // 1/0 di DB, true/false di app
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relasi ke user pembuat.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relasi ke user pengubah terakhir.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope status.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 0);
    }

    /**
     * Accessor boolean: $category->is_active
     */
    public function getIsActiveAttribute(): bool
    {
        // karena sudah di-cast boolean, cukup kembalikan langsung
        return (bool) $this->status;
    }

    /**
     * (Opsional) Normalisasi input status apa pun (true/'1'/1) -> 1, selain itu 0.
     */
    public function setStatusAttribute($value): void
    {
        $this->attributes['status'] = filter_var($value, FILTER_VALIDATE_BOOL) ? 1 : 0;
    }

    /**
     * Isi otomatis created_by & updated_by bila user login.
     */
    protected static function booted(): void
    {
        static::creating(function (self $model) {
            if (Auth::check()) {
                $model->created_by = $model->created_by ?? Auth::id();
                $model->updated_by = $model->updated_by ?? Auth::id();
            }
        });

        static::updating(function (self $model) {
            if (Auth::check()) {
                $model->updated_by = Auth::id();
            }
        });
    }
}
