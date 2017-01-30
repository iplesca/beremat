<?php

namespace App\BreweryDbApi;

use Pintlabs\Service\Brewerydb as Client;
use Pintlabs\Service\Brewerydb\ApiLimit;
use Pintlabs\Service\Brewerydb\Exception;
use App\BreweryDbApi\BreweryDbApiErrorCodes;
use Illuminate\Support\Facades\Log;

class BreweryDbApi 
{
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
    
    private $counter;
    private $config;
    
    /**
     * Creates wrapper for the BreweryDB API
     * Config available at /config/brewerydb.php
     * 
     * @param array $config
     */
    public function __construct($config)
    {
        $this->config = $config;
        $this->apiClient = new Client($config['api_key'], $config['url']);
        $this->apiClient->setFormat('php');
    }
    public function setCounter(\App\BreweryDbApi\BreweryDbCounter $cnt)
    {
        // init counter
        $data = $cnt::find(1);
        
        if (!$data) {
            // first installation
            $data = $cnt;
            $data->current_count = 0;
            
            $a = \DateTime::createFromFormat('Y-m-d H:i:s', $this->config['api_start_interval'], new \DateTimeZone( $this->config['api_timezone'] ));
            $sTimestamp = $a->getTimestamp();
            
//            echo 'time() = ' . time() . '<br>';
//            echo '$sTimestamp = ' . $sTimestamp . '<br>';
//            echo 'time() - $sTimestamp = ' . (time() - $sTimestamp) . '<br>';
//            echo 'times = ' . ((time() - $sTimestamp) / $this->config['api_reset_interval']) . '<br>';
            
            // find new reset time
            $times = round((time() - $sTimestamp) / $this->config['api_reset_interval'], 0, PHP_ROUND_HALF_DOWN) + 1;
            $firstReset = $sTimestamp + $times * $this->config['api_reset_interval'];
            
            $b = new \DateTime();
            $b->setTimezone(new \DateTimeZone(config('app.timezone')));
            $b->setTimestamp($firstReset);
            
            $data->reset_time = $b->format('Y-m-d H:i:s');
            
            unset($a, $b);
            
            $data->save();
        } else {
            // check reset conditions
            $now = time();
            $a = \Datetime::createFromFormat('Y-m-d H:i:s', $data->reset_time, new \DateTimeZone(config('app.timezone')));
            $resetTime = $a->getTimestamp();
            
            if ($now >= $resetTime) {
                $data->current_count = 0;
                // add new api_reset_interval
                $a->setTimestamp($resetTime + $this->config['api_start_interval']);
                $data->reset_time = $a->format('Y-m-d H:i:s');
                
                $data->save();
            }
            unset($a);
        }
        
        $this->counter = $data;
    }
    /**
     * Fetches a random beer
     * 
     * @return false|BeerEntity
     */
    public function getRandomBeer($count = 1)
    {
        $params = [
            'availableId'   => 1,
            'order'         => 'random',
            'randomCount'   => $count,
            'withBreweries' => 'Y'
        ];
        $result = $this->apiRequest('beers', $params);

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
            throw new Exception('Brewery ID must not be empty', BreweryDbApiErrorCodes::ERR_BREWERY_ID);
        }
        $params = [
            'withBreweries' => 'Y'
        ];
        
        $result = $this->apiRequest("brewery/$breweryId/beers", $params);
        
        return $result;
    }
    public function search($pattern, $type, $page = 1)
    {
        // ensure proper patern
        if (empty($pattern) || empty($type)) {
            throw new Exception('Search criteria must not be empty', BreweryDbApiErrorCodes::ERR_SEARCH_EMPTY);
        }
        // only specific types
        if (false === in_array($type, ['beer', 'brewery'])) {
            throw new Exception('Search type must be either `beer` or `brewery`', BreweryDbApiErrorCodes::ERR_SEARCH_TYPE);
        }
        
        $result = false;
        $params = [
            'p'    => $page,    // i'll stick to the first page for the moment => no pagination
            'q'    => $pattern,
            'type' => $type,
        ];
        
        $result = $this->apiRequest("search", $params);
        
        return $result;
    }
    public function isRequestLimitReached()
    {
        return ($this->config['api_daily_requests'] <= $this->currentRequests()) ? true : false;
    }
    //----------------
    // PRIVATE SHTUFF
    //----------------
    
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
        // check API request limit
        if ($this->isRequestLimitReached()) {
            throw new ApiLimit('BreweryDB API daily requests limit reached.', BreweryDbApiErrorCodes::ERR_REQUEST_LIMIT);
        }
        
        try {
            $result = $this->apiClient->request($endpoint, $params);
            $this->incrementRequests();
            
            // keep the data block - for the moment I don't need the rest
            $result = $result['data'];
            
            if ($this->logResults) {
                Log::notice(sprintf('[%s]\nparams:\n%s\nAPI response\n%s', $endpoint, var_export($params, true), var_export($result, true)));
            }
            
        } catch (\Exception $e) {
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
    private function currentRequests()
    {
        return $this->counter->current_count;
    }
    private function incrementRequests()
    {
        $this->counter->current_count++;
        $this->counter->save();
    }
}