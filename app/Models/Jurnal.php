<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Jurnal extends Model
{
    protected $table = 'jurnal';
    protected $guarded = ['id'];

    public function akunTrans(): HasMany
    {
        return $this->hasMany(AkunTrans::class, 'jurnal_id');
    }

    public function cabang(): BelongsTo
    {
        return $this->belongsTo(Cabang::class, 'cabang_id');
    }
}
