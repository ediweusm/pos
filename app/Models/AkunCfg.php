<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AkunCfg extends Model
{
    protected $table = 'akun_cfg';
    protected $guarded = [];

    public function akunDebit(): BelongsTo
    {
        return $this->belongsTo(AkunMaster::class, 'akun_debit_id');
    }

    public function akunKredit(): BelongsTo
    {
        return $this->belongsTo(AkunMaster::class, 'akun_kredit_id');
    }
}
