<?php

class ThermalFormatter
{
    static function make($data)
    {
        $esc = "\x1B";
        $txt = $esc . "@"; // init
        $txt .= $esc . "a" . chr(1); // center
        $txt .= "SONI ELEKTRONIK\n";
        $txt .= $esc . "a" . chr(0); // left

        foreach ($data['items'] as $item) {
            $txt .= $item['nama'] . "\n";
            $txt .= $item['qty'] . " x " . $item['harga'] . " = " . $item['total'] . "\n";
        }

        $txt .= "\n\n\n" . $esc . "i"; // cut paper
        return $txt;
    }
}
