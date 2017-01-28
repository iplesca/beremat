<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Beer extends Model
{
    const DEFAULT_IMAGE_ICON   = 'images/beer-glass-64.jpg';
    const DEFAULT_IMAGE_MEDIUM = 'images/beer-glass-256.jpg';
    const DEFAULT_IMAGE_LARGE  = 'images/beer-glass-512.jpg';
    
    public $primaryKey = 'api_id';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = ['api_id', 'brewery_api_id', 'name', 'description', 'abv', 'abv_min', 
        'abv_max', 'image_icon', 'image_medium', 'image_large', 'status'];
    
    public function dataFromApi($beerData)
    {
        $this->api_id = $beerData['id'];
        $this->name   = $beerData['name'];
        $this->status = $beerData['status'];
        
        // some defaults
        $this->image_icon   = self::DEFAULT_IMAGE_ICON;
        $this->image_medium = self::DEFAULT_IMAGE_MEDIUM;
        $this->image_large  = self::DEFAULT_IMAGE_LARGE;
        
        // brewery_api_id
        if (isset($beerData['breweries']) && isset($beerData['breweries'][0])) {
            $this->brewery_api_id = $beerData['breweries'][0]['id'];
        }
        // description
        if (isset($beerData['description']) && !empty($beerData['description'])) {
            $this->description = $beerData['description'];
        }
        // abv
        if (isset($beerData['abv']) && !empty($beerData['abv'])) {
            $this->abv = $beerData['abv'];
        }
        // abv_min
        if (isset($beerData['abvMin']) && !empty($beerData['abvMin'])) {
            $this->abv_min = $beerData['abvMin'];
        }
        // abv_max
        if (isset($beerData['abvMax']) && !empty($beerData['abvMax'])) {
            $this->abv_max = $beerData['abvMax'];
        }
        // images
        if (isset($beerData['labels'])) {
            $this->image_icon   = isset($beerData['labels']['icon']) ? $beerData['labels']['icon'] : self::DEFAULT_IMAGE_ICON;
            $this->image_medium = isset($beerData['labels']['medium']) ? $beerData['labels']['medium'] : self::DEFAULT_IMAGE_MEDIUM;
            $this->image_large  = isset($beerData['labels']['large']) ? $beerData['labels']['large'] : self::DEFAULT_IMAGE_LARGE;
        }
        // quick validation
        if (empty($this->description)) {
            return false;
        }
        return true;
    }
}
