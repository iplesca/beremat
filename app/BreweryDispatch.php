<?php

namespace App;

class BreweryDispatch
{
    /**
     * Transforms a raw API brewery response result into a "brewery collection"
     * 
     * @param array $breweries
     * @return array
     */
    static public function generateBreweryEntities($breweries)
    {
        $result = [];
        
        foreach ($breweries as $br) {
            $nBrewery = new \App\Brewery();
            
            if ($nBrewery->dataFromApi($br)) {
                $result[] = $nBrewery->toArray();
            }
        }
        return $result;
    }
    private function saveBreweries2Local(array $breweries)
    {
        foreach ($breweries as $br) {
            \App\Brewery::firstOrCreate($br);
        }
    }
}