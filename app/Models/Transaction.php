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
        'titipan',
        'sisa_pembayaran',
        'status_pembayaran',
    ];

    /**
     * Casts ensure `tanggal` is treated as a Carbon instance when accessed.
     */
    protected $casts = [
        'tanggal' => 'date',
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
