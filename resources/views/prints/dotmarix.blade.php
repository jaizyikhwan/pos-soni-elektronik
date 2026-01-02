@php
    function pad($text, $length, $padType = STR_PAD_RIGHT)
    {
        return str_pad(substr($text, 0, $length), $length, ' ', $padType);
    }
@endphp

TOKO SONI ELEKTRONIK
Jl. Demuk No. 123, Tulungagung

KEPADA YTH:
{{ $nama_pembeli }}
{{ $alamat }}
TELP : {{ $no_hp }}
{{ pad('TANGGAL', 15) }}: {{ $tanggal }}

--------------------------------------------------------------------------------
{{ pad('NO', 4) }}
{{ pad('NAMA BARANG', 25) }}
{{ pad('TIPE', 10) }}
{{ pad('NO SERI', 18) }}
{{ pad('QTY', 5) }}
{{ pad('HARGA', 12, STR_PAD_LEFT) }}
{{ pad('TOTAL', 14, STR_PAD_LEFT) }}
--------------------------------------------------------------------------------
@foreach ($transactions as $i => $trx)
    {{ pad($i + 1, 4) }}
    {{ pad($trx->item->nama_barang, 25) }}
    {{ pad($trx->item->tipe_barang ?? '-', 10) }}
    {{ pad($trx->nomor_seri ?? '-', 18) }}
    {{ pad($trx->jumlah, 5) }}
    {{ pad(number_format($trx->harga_satuan, 0, ',', '.'), 12, STR_PAD_LEFT) }}
    {{ pad(number_format($trx->total_harga, 0, ',', '.'), 14, STR_PAD_LEFT) }}
@endforeach
--------------------------------------------------------------------------------
TOTAL {{ pad('', 45) }}Rp {{ number_format($total, 0, ',', '.') }}

@if ($status === 'DP')
    TITIPAN {{ pad('', 42) }}Rp {{ number_format($titipan, 0, ',', '.') }}
    SISA {{ pad('', 42) }}Rp {{ number_format($sisa, 0, ',', '.') }}
    STATUS {{ pad('', 42) }}DP
@else
    STATUS {{ pad('', 42) }}LUNAS
@endif

BANK BCA : 4480 880 250
A/N : SONI STIAWAN

( TERIMA KASIH )
