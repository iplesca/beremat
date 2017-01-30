<?php

namespace App\BreweryDbApi;

use Illuminate\Database\Eloquent\Model;

class BreweryDbCounter extends Model
{
    protected $table = 'api_counter';
    public $timestamps = false;
    
    public function reset($cnt)
    {
        $cnt->current_count = 0;
        $cnt->save();
    }
}