<?php

namespace App;

use Pintlabs\Service\Brewerydb\Exception;

class BeerDispatch
{
    const SESSION_RANDOM_BEER = 'randomBeerList';
    const BEER_RANDOM_COUNT = 10;
    
    public function getRandomBeer()
    {
        // fetch more
        if (empty($this->session())) {
            // fetch more from the API
            $beers = App('BreweryDbApi')->getRandomBeer(self::BEER_RANDOM_COUNT);
            if ($beers) {
                // transform to Beer entities
                $beers = self::generateBeerEntities($beers);
                // save to local db 
                $this->saveBeers2Local($beers);
            } else {
                $beers = [];
            }

            $this->session($beers);
        }
        $result = $this->getRandomBeerFromSession();
        
        return $result;
    }
    public function getBeersFromSameBrewery($currBeer)
    {
        // call the API directly (db support laterz)
        try {
            $result = App('BreweryDbApi')->getBeersByBreweryId($currBeer['brewery_api_id']);
            
        } catch (Exception $e) {
            $result = false;

            switch ($e->getCode()) {
                case \App\BreweryDbApi\BreweryDbApiErrorCodes::ERR_BREWERY_ID : 
                    session()->flash('errorMessage', 'Brewery was not found.');
                    break;
            }
        }
        if ($result) {
            $result = self::generateBeerEntities($result, $currBeer['brewery_api_id']);
            $this->saveBeers2Local($result);
        }
        
        return $result;
    }
    /**
     * Fetch and remove one random entry from the beer session
     * 
     * @return App\Beer
     */
    private function getRandomBeerFromSession()
    {
        $beers = $this->session();
        
        if (count($beers)) {
            $key = array_rand($beers);
            $result = $beers[$key];

            unset($beers[$key]);

            $this->session($beers);
        } else {
            $result = false;
        }
        
        return $result;
    }
    private function session($content = null)
    {
        if (is_null($content)) {
            return session()->get(self::SESSION_RANDOM_BEER);
        } else {
            session()->put(self::SESSION_RANDOM_BEER, $content);
        }
    }
    /**
     * Transforms a raw API beer response result into a BeerCollection
     * 
     * @param array $beers
     * @param mixed $breweryId
     * @return array
     */
    static public function generateBeerEntities($beers, $breweryId = null)
    {
        $result = [];
        
        foreach ($beers as $b) {
            
            // use this hack to avoid the premium-glitch issue of endpoints /breweries/:id/beers not returning this data
            if (!is_null($breweryId)) {
                $b['breweries'][0]['id'] = $breweryId;
            }
            
            $nBeer = new \App\Beer();
            
            // save only if data is valid
            // @todo: do validation better
            if ($nBeer->dataFromApi($b)) {
                $result[] = $nBeer->toArray();
            }
        }
        return $result;
    }
    private function saveBeers2Local(array $beers)
    {
        foreach ($beers as $b) {
            \App\Beer::firstOrCreate($b);
        }
    }
}