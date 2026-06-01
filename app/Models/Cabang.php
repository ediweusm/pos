<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cabang extends Model
{
    protected $table = 'cabang';
    protected $guarded = ['id'];

    public function gudangs(): HasMany
    {
        return $this->hasMany(Gudang::class, 'cabang_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'cabang_id');
    }

    public function jurnals(): HasMany
    {
        return $this->hasMany(Jurnal::class, 'cabang_id');
    }
}
