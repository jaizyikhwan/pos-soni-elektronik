<?php

class DotmatrixFormatter
{
    static function make($data)
    {
        $text = "SONI ELEKTRONIK\n";
        $text .= "Jl. Demuk No. 123\n";
        $text .= str_repeat("-", 80) . "\n";

        foreach ($data['items'] as $i => $item) {
            $text .= str_pad($i + 1, 4);
            $text .= str_pad($item['nama'], 25);
            $text .= str_pad($item['qty'], 5);
            $text .= str_pad(number_format($item['total']), 14, ' ', STR_PAD_LEFT) . "\n";
        }

        return $text;
    }
}
