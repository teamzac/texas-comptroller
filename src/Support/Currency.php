<?php

namespace TeamZac\TexasComptroller\Support;

class Currency
{
    /**
     * Remove punctionation from a number string
     *
     * @param string $text
     * @return double
     */
    public static function clean($text) 
    {
        return (double) str_replace([','], '', trim($text));
    }
}
