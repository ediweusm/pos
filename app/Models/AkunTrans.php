<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AkunTrans extends Model
{
    protected $table = 'akun_trans';
    protected $guarded = ['id'];

    public function jurnal(): BelongsTo
    {
        return $this->belongsTo(Jurnal::class, 'jurnal_id');
    }

    public function akun(): BelongsTo
    {
        return $this->belongsTo(AkunMaster::class, 'akun_id');
    }
}
