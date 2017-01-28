<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Brewery extends Model
{
    const DEFAULT_IMAGE_ICON   = 'images/brewery-64.jpg';
    const DEFAULT_IMAGE_MEDIUM = 'images/brewery-256.jpg';
    const DEFAULT_IMAGE_LARGE  = 'images/brewery-512.jpg';
    
    public $primaryKey = 'api_id';
    public $incrementing = false;
    public $timestamps = false;
    
    protected $fillable = ['api_id', 'name', 'description', 'website', 'image_icon', 'image_medium', 'image_large', 'status'];
    
    public function dataFromApi($breweryData)
    {
        $this->api_id = $breweryData['id'];
        $this->name   = $breweryData['name'];
        $this->status = $breweryData['status'];
        
        // some defaults
        $this->image_icon   = self::DEFAULT_IMAGE_ICON;
        $this->image_medium = self::DEFAULT_IMAGE_MEDIUM;
        $this->image_large  = self::DEFAULT_IMAGE_LARGE;
        
        // description
        if (isset($breweryData['description']) && !empty($breweryData['description'])) {
            $this->description = $breweryData['description'];
        }
        // images
        if (isset($breweryData['images'])) {
            $this->image_icon   = isset($breweryData['images']['icon']) ? $breweryData['images']['icon'] : self::DEFAULT_IMAGE_ICON;
            $this->image_medium = isset($breweryData['images']['medium']) ? $breweryData['images']['medium'] : self::DEFAULT_IMAGE_MEDIUM;
            $this->image_large  = isset($breweryData['images']['large']) ? $breweryData['images']['large'] : self::DEFAULT_IMAGE_LARGE;
        }
        // quick validation
        if (empty($this->description)) {
            return false;
        }
        return true;
    }
}
