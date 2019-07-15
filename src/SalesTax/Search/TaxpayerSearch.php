<?php

namespace TeamZac\TexasComptroller\SalesTax\Search;

use TeamZac\TexasComptroller\BaseReports\HttpReport;
use TeamZac\TexasComptroller\SalesTax\Search\Taxpayer;

class TaxpayerSearch extends HttpReport
{
    /** @var string */
    protected $baseUri = 'https://mycpa.cpa.state.tx.us/staxpayersearch/';

    /**
     * 
     * 
     * @param   
     * @return  
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
     * 
     * 
     * @param   
     * @return  
     */
    public function parseResponse($response)
    {
        $dom = str_get_html($response);

        $attributes = $this->findTaxpayer($dom->find('.panel-body', 0));

        $locations = $this->findLocations($dom->find('.panel-body', 1));

        return (new Taxpayer($attributes))->setLocations($locations);;
    }

    /**
     * 
     * 
     * @param   
     * @return  
     */
    public function findTaxpayer($dom)
    {
        $attributes = [
            'id' => trim($dom->find('.row .col-sm-9', 0)->innertext),
            'name' => trim($dom->find('.row .col-sm-9', 1)->innertext),
            'address' => $this->parseTaxpayerAddress($dom->find('.row .col-sm-9', 2)->innertext, $dom->find('.row .col-sm-9', 3)->innertext),
            'status' => trim($dom->find('.row .col-sm-9 span', 0)->innertext)
        ];

        return $attributes;
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
    public function findLocations($dom)
    {
        $locations = [];

        foreach ($dom->find('table', 0)->children as $tableSection)
        {
            // simple dom won't skip past the thead, so we will do it manually
            if ($tableSection->tag == 'thead') {
                continue;
            }

            foreach ($tableSection->children as $row) {
                // dd($row->innertext);
                // dd($row->find('td', 1)->innertext);
                $locations[] = [
                    'name' => trim($row->find('td', 0)->innertext),
                    'status' => trim($row->find('td', 1)->find('span', 0)->innertext),
                    'address' => $this->parseTaxpayerAddress(
                        substr($addressField = $row->find('td', 2)->innertext, 0, strpos($addressField, '<span')),
                        $row->find('td', 3)->innertext
                    ),
                    'number' => trim($row->find('td', 4)->innertext),
                    'open_at' => trim($row->find('td', 5)->innertext),
                    'closed_at' => trim($row->find('td', 6)->innertext),
                ];
            }
        }
        return collect($locations);
    }
}
