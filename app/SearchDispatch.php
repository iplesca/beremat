<?php

namespace App;

use Pintlabs\Service\Brewerydb\Exception;

class SearchDispatch
{
    const SEARCH_TYPE_BEER    = 'beer';
    const SEARCH_TYPE_BREWERY = 'brewery';
    
    public function search($query, $type)
    {
        $useApi = true;
        
        if ($useApi) {
            return $this->searchUsingApi($query, $type);
        } else {
//            return $this->searchUsingDb($query, $type);
        }
    }
    private function searchUsingApi($query, $type)
    {
        try {
            $result = App('BreweryDbApi')->search($query, $type);
            
        } catch (Exception $e) {
            $result = false;
            session()->flash('errorMessage', $e->getMessage());
        }
        
        if ($result) {
            // prepare search results based on source input
            switch ($type) {
                case self::SEARCH_TYPE_BEER :
                    // first, transform the raw API response into a "beer collection"
                    $result = \App\BeerDispatch::generateBeerEntities($result);
                    
                    // transform the Beer Collection to search results
                    $result = self::generateResultsFromBeerCollection($result);
                    break;
                case self::SEARCH_TYPE_BREWERY : 
                    // first, transform the raw API response into a "brewery collection"
                    $result = \App\BreweryDispatch::generateBreweryEntities($result);
                    
                    $result = self::generateResultsFromBreweryCollection($result);
                    break;
            }
        }
        
        return $result;
    }
    /**
     * Generates an appropriate $searchResult structure
     * $beerCollection elements should have an App\Beer structure 
     * 
     * @param array $beerCollection
     */
    static public function generateResultsFromBeerCollection(array $beerCollection)
    {
        $result = [];
        foreach ($beerCollection as $b) {
            $result[] = [
                'id'          => $b['api_id'],
                'name'        => $b['name'],
                'description' => $b['description'],
                'image'       => $b['image_icon']
            ];
        }
        return $result;
    }
    /**
     * Generates an appropriate $searchResult structure
     * $breweryCollection elements should have an App\Brewery structure 
     * 
     * @param array $breweryCollection
     */
    static public function generateResultsFromBreweryCollection(array $breweryCollection)
    {
        // [hack] same as the "beer collection" ... for now
        return self::generateResultsFromBeerCollection($breweryCollection);
    }
}