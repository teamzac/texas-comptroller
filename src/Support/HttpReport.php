<?php

namespace TeamZac\TexasComptroller\Support;

use GuzzleHttp\Client as Guzzle;
use TeamZac\TexasComptroller\Exceptions\HttpException;
use TeamZac\TexasComptroller\Exceptions\InvalidRequest;

class HttpReport
{
    /** @var string */
    protected $baseUri = 'https://mycpa.cpa.state.tx.us/allocation/';

    /** @var array */
    protected $params;

    /** @var string */
    protected $endpoint;

    /**
     * Convenience constructor
     * 
     * @return  HttpReport
     */
    public static function make()
    {
        return new static;
    }

    /**
     * Return the endpoing
     * 
     * @return  string
     */
    public function getEndpoint()
    {
        return $this->endpoint;
    }

    /**
     * Return the parameters
     * 
     * @return  array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Get an HTTP client
     * 
     * @return  Guzzle\Client
     */
    public function http()
    {
        return new Guzzle([
            'base_uri' => $this->baseUri,
            'cookies' => true
        ]);
    }

    /**
     * Fetch the report from the Comptroller's web site using the parameters that have been provided
     * 
     * @return  mixed
     */
    public function get()
    {
        if ( $this->endpoint === null ) {
            throw new InvalidRequest;
        }

        if ( $this->params === null ) {
            throw new InvalidRequest;
        }

        return $this->parseResponse($this->submitFormRequest());
    }

    /**
     * Submits the form request and returns the results
     *
     * @return  GuzzleHttp\Psr7\Stream
     */
    protected function submitFormRequest()
    {
        try {
            $response = $this->http()->post($this->endpoint, [
                'form_params' => $this->params
            ])->getBody();
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            throw HttpException::fromClientException($e);
        }

        return $this->processRawResponse($response);
    }

    /**
     * Process the raw Psr7 response response
     * 
     * @param   GuzzleHttp\Psr7\Stream
     * @return  mixed
     */
    public function processRawResponse($response)
    {
        return (string) $response;
    }

    /**
     * Parse the response and return the results, to be overriden by subclasses   
     *
     * @param   string $response
     * @return  mixed
     */
    protected function parseResponse($response)
    {
        throw new \Exception(get_class($this) . ' should override the parseResponse() method');
    }
}
