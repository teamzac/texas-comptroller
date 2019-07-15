<?php

namespace TeamZac\TexasComptroller\BaseReports;

class JsonReport extends HttpReport
{
    /**
     * Process the raw Psr7 response response
     * 
     * @param   GuzzleHttp\Psr7\Stream
     * @return  mixed
     */
    public function processRawResponse($response)
    {
        return json_decode((string) $response);
    }
}
