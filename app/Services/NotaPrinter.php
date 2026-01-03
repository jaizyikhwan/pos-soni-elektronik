<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;

class NotaPrinter
{
    public function print(string $printer, array $transactions, array $meta): void
    {
        [$view, $printerName] = match ($printer) {
            'thermal' => ['prints.thermal', config('printer.thermal')],
            'dotmatrix' => ['prints.dotmatrix', config('printer.dotmatrix')],
            default => throw new \Exception('Printer tidak dikenali'),
        };

        $notaText = View::make($view, [
            'tanggal'      => $meta['tanggal'],
            'nama_pembeli' => strtoupper($meta['nama_pembeli']),
            'no_hp'        => strtoupper($meta['no_hp']),
            'alamat'       => strtoupper($meta['alamat']),
            'transactions' => $transactions,
            'total'        => $meta['total'],
            'titipan'      => $meta['titipan'],
            'sisa'         => $meta['sisa'],
            'status'       => $meta['status'],
        ])->render();

        $filename = storage_path('app/nota_' . now()->timestamp . '.txt');
        File::put($filename, $notaText);

        $this->sendToPrinter($printerName, $filename);
    }

    private function sendToPrinter(string $printerName, string $file): void
    {
        if (!in_array(PHP_OS_FAMILY, ['Linux', 'Darwin'])) {
            Log::warning('Printing dilewati, OS tidak didukung', [
                'os' => PHP_OS_FAMILY
            ]);
            return;
        }

        exec(
            sprintf(
                'lp -d %s %s',
                escapeshellarg($printerName),
                escapeshellarg($file)
            ),
            $output,
            $result
        );

        if ($result !== 0) {
            Log::error('Gagal mencetak nota', compact('printerName', 'file', 'output'));
        }
    }
}
