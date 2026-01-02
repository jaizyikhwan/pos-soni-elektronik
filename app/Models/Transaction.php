<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'item_id',
        'jumlah',
        'nama_pembeli',
        'no_hp',
        'alamat',
        'total_harga',
        'harga_satuan',
        'nomor_seri',
        'tanggal',
        'status',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function isCancelled()
    {
        return $this->status === 'CANCELLED';
    }
}
