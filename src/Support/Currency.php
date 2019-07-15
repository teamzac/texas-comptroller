<?php

namespace TeamZac\TexasComptroller\Support;

class Currency
{
    public static function clean($text) 
    {
        return (double) str_replace([','], '', trim($text));
    }
}
