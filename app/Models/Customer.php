<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    // Mendaftarkan kolom yang boleh diisi massal sesuai modul
    protected $fillable = [
        'customer_id',
        'name',
        'email',
        'phone',
        'address',
        'status'
    ];

    // Otomatis mengonversi status menjadi boolean (true/false)
    protected function casts(): array
    {
        return [
            'status' => 'boolean',
        ];
    }

    /**
     * Relasi ke model Subscription (Satu customer bisa mengambil banyak subscription)
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }
}