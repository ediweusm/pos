<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KasOperasional extends Model
{
    protected $table = 'kas_operasional';
    protected $guarded = ['id'];

    public function akunKas(): BelongsTo
    {
        return $this->belongsTo(AkunMaster::class, 'akun_kas_id');
    }

    public function akunLawan(): BelongsTo
    {
        return $this->belongsTo(AkunMaster::class, 'akun_lawan_id');
    }
}
