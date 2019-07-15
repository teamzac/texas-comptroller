<?php

namespace TeamZac\TexasComptroller\Exceptions;

use GuzzleHttp\Exception\ClientException;

class HttpException extends \RuntimeException 
{
    public $statusCode;

    public $url;

    /**
     * 
     * 
     * @param   
     * @return  
     */
    public static function fromClientException(ClientException $e)
    {
        return (new static($e->getMessage()))
            ->setStatusCode($e->getResponse()->getStatusCode())
            ->setUrl((string) $e->getRequest()->getUri());
    }

    /**
     * Set the status code
     * 
     * @param   int $code
     * @return  $this
     */
    public function setStatusCode($code)
    {
        $this->statusCode = $code;
        return $this;
    }

    /**
     * Set the URL
     * 
     * @param   string $url
     * @return  $this
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }
}
