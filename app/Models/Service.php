<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    // Mengatur kolom yang boleh diisi secara massal
    protected $fillable = ["name", "price", "description", "status"];

    // Mengonversi tipe data secara otomatis saat diakses
    protected function casts(): array
    {
        return [
            "status" => "boolean",
            "price" => "integer",
        ];
    }

    /**
     * Relasi ke model Subscription (Satu service bisa memiliki banyak subscription)
     * @return HasMany
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }
}