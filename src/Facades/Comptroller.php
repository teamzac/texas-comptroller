<?php

namespace TeamZac\TexasComptroller\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Teamzac\TexasComptroller\TexasComptroller
 */
class Comptroller extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'texas-comptroller';
    }
}
