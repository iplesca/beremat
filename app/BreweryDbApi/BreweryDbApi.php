<?php

namespace App\BreweryDbApi;

use Pintlabs\Service\Brewerydb as Client;
use App\BreweryDbApi\BreweryDbApiErrorCodes;
use Illuminate\Support\Facades\Log;

class BreweryDbApi 
{
    /**
     * How many times should retry an API call
     */
    const MAX_RETRIES = 3;
    /**
     * Client to talk to BreweryDB API
     * @var Pintlabs\Service\Brewerydb
     */
    protected $apiClient;
    /**
     * Flag to write api responses
     * @var bool
     */
    private $logResults = true;
    
    /**
     * Creates wrapper for the BreweryDB API
     * Config available at /config/brewerydb.php
     * 
     * @param array $config
     */
    public function __construct($config)
    {
        $this->apiClient = new Client($config['api_key'], $config['url']);
        $this->apiClient->setFormat('php');
    }
    /**
     * Fetches a random beer
     * 
     * @return false|BeerEntity
     */
    public function getRandomBeer()
    {
        $params = [
            'availableId'   => 1,
            'order'         => 'random',
            'randomCount'   => 1,
            'withBreweries' => 'Y'
        ];
        // loop until we get a beer with description
        $retriesLeft = self::MAX_RETRIES;
        do {
            $result = $this->apiRequest('beers', $params);

            if (false !== $result && count($result)) {
                $result = $result[0];
            }
        } while (!$this->isValidBeerEntity($result) || 0 != --$retriesLeft);
        
        if (!$this->isValidBeerEntity($result)) {
            throw new \Exception('Gave up ['. self::MAX_RETRIES .' retries] in finding a valid beer', BreweryDbApiErrorCodes::ERR_PROPER_BEER);
        }
        $result = $this->generateCollection($result);
        
        return $result;
    }
    /**
     * Returns an array of "beer entities" for a given breweryId
     * 
     * @param string $breweryId
     * @return false|array
     * @throws Exception
     */
    public function getBeersByBreweryId($breweryId)
    {
        // ensure proper id
        if (empty($breweryId)) {
            throw new \Exception('Brewery ID must not be empty', BreweryDbApiErrorCodes::ERR_BREWERY_ID);
        }
        $result = false;
        $params = [
            'withBreweries' => 'Y'
        ];
        
        $beers = $this->apiRequest("brewery/$breweryId/beers", $params);
        
        if (!empty($beers)) {
            $result = [];
            foreach ($beers as $b) {
                // keep only valid beer records (with description and label)
                if ($this->isValidBeerEntity($b)) {
                    $result[] = $this->generateCollection($b);
                }
            }
        }
            
        return $result;
    }
    public function search($pattern, $type, $page = 1)
    {
        // ensure proper patern
        if (empty($pattern) || empty($type)) {
            throw new \Exception('Search criteria must not be empty', BreweryDbApiErrorCodes::ERR_SEARCH_EMPTY);
        }
        // only specific types
        if (false === in_array($type, ['beer', 'brewery'])) {
            throw new \Exception('Search type must be either `beer` or `brewery`', BreweryDbApiErrorCodes::ERR_SEARCH_TYPE);
        }
        
        $result = false;
        $params = [
            'p'    => $page,    // i'll stick to the first page for the moment, no pagination
            'q'    => $pattern,
            'type' => $type,
        ];
        
        $result = $this->apiRequest("search", $params);
        
        // clean the bloody empty-description
        foreach ($result as $k => $v) {
            if (!isset($v['description'])) {
                unset($result[$k]);
            } else {
                $result[$k] = $this->generateCollection($v);
            }
        }
        
        return $result;
    }
    
    //////////
    // PRIVATE
    //////////
    
    /**
     * Calls internal $apiClient with given endpoint and params
     * Handles API exceptions
     * 
     * @param string $endpoint
     * @param array $params
     * @return false|array
     */
    private function apiRequest($endpoint, array $params = [])
    {
        try {
            $result = $this->apiClient->request($endpoint, $params);
            // keep the data block - for the moment I don't need the rest
            $result = $result['data'];
            
            if ($this->logResults) {
                Log::notice(sprintf('[%s]\nparams:\n%s\nAPI response\n%s', $endpoint, var_export($params, true), var_export($result, true)));
            }
            
        } catch (Exception $e) {
            $result = false;
            /**
             * @link https://groups.google.com/d/msg/brewerydb-api/7dhWPu3cFjg/d2kw5WhOeT0J
             */
            Log::warning(sprintf('[API FAIL][%s] %s', $e->getCode(), $e->getMessage()));
            if ($this->logResults) {
                Log::notice(sprintf('[API REQUEST][%s]\nparams:\n%s', $endpoint, var_export($params, true)));
            }
        }
        return $result;
    }
    /**
     * Checks if $beer has:
     *  - description
     *  - image label
     * 
     * @param array $beer
     * @return type
     */
    private function isValidBeerEntity($beer)
    {
        return (isset($beer['description']) && isset($beer['labels'])) ? true : false;
    }
    /**
     * Creates an internal "beer entity" (with defaults) from an API beer record
     * NOTE: also unifies the beer/brewery images v. labels mess
     * 
     * @param BeerApiResult $beer
     * @return BeerEntity
     */
    private function generateCollection($resultNode)
    {
        $result['name'] = false;
        $result['description'] = false;
        $result['brewery'] = false;
        // seems default labels are not needed
        $result['images'] = [
            'icon'   => asset('images/beer-glass-64.jpg'),
            'medium' => asset('images/beer-glass-256.jpg'),
            'large'  => asset('images/beer-glass-512.jpg'),
        ];
        
        if (isset($resultNode['name'])) {
            $result['name'] = $resultNode['name'];
        }
        if (isset($resultNode['description'])) {
            $result['description'] = $resultNode['description'];
        }
        // if there are LABELS defined
        if (isset($resultNode['labels'])) {
            if (isset($resultNode['labels']['icon'])) {
                $result['images']['icon'] = $resultNode['labels']['icon'];
            }
            if (isset($resultNode['labels']['medium'])) {
                $result['images']['medium'] = $resultNode['labels']['medium'];
            }
            if (isset($resultNode['labels']['large'])) {
                $result['images']['large'] = $resultNode['labels']['large'];
            }
        }
        // if there are IMAGES defined
        if (isset($resultNode['images'])) {
            if (isset($resultNode['labels']['icon'])) {
                $result['images']['icon'] = $resultNode['images']['icon'];
            }
            if (isset($resultNode['labels']['medium'])) {
                $result['images']['medium'] = $resultNode['images']['medium'];
            }
            if (isset($resultNode['labels']['large'])) {
                $result['images']['large'] = $resultNode['images']['large'];
            }
        }
        if (isset($resultNode['breweries']) && isset($resultNode['breweries'][0])) {
            $result['brewery'] = [
                'name' => $resultNode['breweries'][0]['name'],
                'id' => $resultNode['breweries'][0]['id'],
            ];
        }
        
        return $result;
    }
}