@php
    function pad($text, $length, $padType = STR_PAD_RIGHT)
    {
        return str_pad(substr($text, 0, $length), $length, ' ', $padType);
    }
@endphp

SONI ELEKTRONIK
Jl. Demuk No. 123, Tulungagung
------------------------------
Tanggal : {{ $tanggal }}
Pembeli : {{ $nama_pembeli }}
No HP : {{ $no_hp }}
Alamat : {{ $alamat }}
------------------------------
Barang (Tipe) Qty Total
@foreach ($transactions as $trx)
    {{ pad($trx->item->nama_barang, 16) }}
    ({{ pad($trx->item->tipe_barang ?? '-', 8) }})
    {{ pad($trx->jumlah, 3) }}
    {{ pad(number_format($trx->total_harga, 0, ',', '.'), 10, STR_PAD_LEFT) }}

    @if ($trx->nomor_seri)
        NO. SERI: {{ $trx->nomor_seri }}
    @endif
@endforeach
------------------------------
TOTAL : Rp {{ number_format($total, 0, ',', '.') }}

@if ($status === 'DP')
    TITIPAN : Rp {{ number_format($titipan, 0, ',', '.') }}
    SISA : Rp {{ number_format($sisa, 0, ',', '.') }}
    STATUS : DP
@else
    STATUS : LUNAS
@endif
------------------------------
Terima kasih 🙏
