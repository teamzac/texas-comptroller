<?php

namespace TeamZac\TexasComptroller\SalesTax\Search;

use Symfony\Component\DomCrawler\Crawler;
use TeamZac\TexasComptroller\BaseReports\HttpReport;
use TeamZac\TexasComptroller\SalesTax\Search\Taxpayer;

class TaxpayerSearch extends HttpReport
{
    /** @var string */
    protected $baseUri = 'https://mycpa.cpa.state.tx.us/staxpayersearch/';

    /**
     * Search for the given taxpayer id
     * 
     * @param   string $taxpayerId
     * @return  Taxpayer
     */
    public function search($taxpayerId)
    {
        $this->endpoint = 'taxpayerIdSearch.do';

        $this->params = [
            'taxpayerId' => $taxpayerId,
        ];

        return $this->get();
    }

    /**
     * Parse the HTML response
     * 
     * @param   string $response
     * @return  Taxpayer
     */
    public function parseResponse($response)
    {
        $crawler = new Crawler($response);
        
        list ($taxpayer, $locations) = $crawler->filter('.panel-body')->each(function(Crawler $node, $i) {
            return $i == 0 ? 
                $this->findTaxpayer($node) :
                $this->findLocations($node);
        });

        return (new Taxpayer($taxpayer))->setLocations($locations);;
    }

    /**
     * 
     * 
     * @param   
     * @return  
     */
    public function findTaxpayer($crawler)
    {
        return [
            'id' => trim($crawler->filter('.panel-body')->children('.row .col-sm-9')->getNode(0)->textContent),
            'name' => trim($crawler->filter('.panel-body')->children('.row .col-sm-9')->getNode(1)->textContent),
            'address' => $this->parseTaxpayerAddress(
                trim($crawler->filter('.panel-body')->children('.row .col-sm-9')->getNode(2)->textContent),
                trim($crawler->filter('.panel-body')->children('.row .col-sm-9')->getNode(3)->textContent)
            ),
            'status' => trim($crawler->filter('.panel-body')->children('.row .col-sm-9 span.label')->getNode(0)->textContent),
        ];
    }

    /**
     * 
     * 
     * @param   
     * @return  
     */
    public function parseTaxpayerAddress($street, $other)
    {
        list($city, $rest) = explode(', ', $other);
        list($state, $zip) = explode(' ', $rest);

        return [
            'street' => $street,
            'city' => $city,
            'state' => $state,
            'zip' => $zip,
        ];
    }

    /**
     * 
     * 
     * @param   
     * @return  
     */
    public function findLocations($crawler)
    {
        $locations = $crawler->filter('tbody tr')->each(function($node) {
            return [
                'name' => trim($node->children()->eq(0)->getNode(0)->textContent),
                'status' => trim($node->children()->eq(1)->getNode(0)->textContent),
                'address' => $this->parseTaxpayerAddress(
                    tap($node->children()->eq(2)->getNode(0), function($n) { 
                        $n->removeChild($n->lastChild);
                    })->textContent,
                    $node->children()->eq(3)->getNode(0)->textContent
                ),
                'number' => trim($node->children()->eq(4)->getNode(0)->textContent),
                'open_at' => trim($node->children()->eq(5)->getNode(0)->textContent),
                'closed_at' => trim($node->children()->eq(6)->getNode(0)->textContent),
            ];
        });
        return collect($locations);
    }
}
