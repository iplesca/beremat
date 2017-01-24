<?php
namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;

use Pintlabs\Service\Brewerydb;

class DashboardController extends BaseController
{
    public function index(Request $request)
    {
        $api = new Brewerydb(config('config.brewerydb_api_key'), config('config.brewerydb_url'));
        $api->setFormat('php');
        
        $api->request('beers', [
            'availableId' => '1',
            'p' => 1,
            'order' => 'random',
            'randomCount' => 10
        ]);
        $a = $api->getLastParsedResponse();
        
        return view('index', [
            'response' => print_r($a, true)
        ]);
    }
}
