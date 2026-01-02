<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{

    use SoftDeletes;

    protected $fillable = [
        'nama_barang',
        'tipe_barang',
        'harga_jual',
        'harga_beli',
        'tanggal_order',
        'stok',
        'barcode'
    ];

    protected $casts = [
        'harga_beli'    => 'integer',
        'harga_jual'    => 'integer',
        'stok'          => 'integer',
        'tanggal_order' => 'date',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function scopeSearch(Builder $query, ?string $search = null): Builder
    {
        if (blank($search)) {
            return $query;
        }

        $keywords = preg_split('/\s+/', trim($search));

        foreach ($keywords as $word) {
            $query->where(function (Builder $q) use ($word) {
                $q->where('nama_barang', 'like', "%{$word}%")
                    ->orWhere('tipe_barang', 'like', "%{$word}%")
                    ->orWhere('barcode', 'like', "%{$word}%");
            });
        }

        return $query;
    }

    protected static function booted()
    {
        static::deleting(function (Item $item) {
            if (! $item->isForceDeleting() && $item->stok !== 0) {
                throw new \LogicException(
                    'Item tidak boleh dihapus jika stok masih ada'
                );
            }
        });
    }

    public function markOutOfStock(): void
    {
        $this->update(['stok' => 0]);
        $this->delete();
    }
}
