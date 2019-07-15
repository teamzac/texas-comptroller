<?php

namespace TeamZac\TexasComptroller\Exceptions;

class InvalidRequest extends \Exception
{
    protected $message = 'The request was incomplete, you should probably use one of the helper methods to fetch a report';
}
