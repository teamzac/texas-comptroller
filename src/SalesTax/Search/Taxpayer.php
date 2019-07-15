<?php

namespace TeamZac\TexasComptroller\SalesTax\Search;

use Illuminate\Support\Arr;

class Taxpayer
{
    protected $attributes = [];

    protected $locations = [];

    public function __construct($attributes = [])
    {
        $this->attributes = $attributes;
    }

    public function setAddress($address) 
    {
        $this->address = $address;
        return $this;
    }

    public function setLocations($locations) 
    {
        $this->locations = collect($locations);
        return $this;
    }

    public function __get($key) 
    {
        return $key == 'locations' ? 
            $this->locations :
            Arr::get($this->attributes, $key);
    }
}
