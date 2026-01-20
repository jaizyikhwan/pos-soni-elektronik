<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;

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
