<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\BreweryDbApi\BreweryDbApi;

class DashboardController extends Controller
{
    const SEARCH_TYPE_BEER    = 'beer';
    const SEARCH_TYPE_BREWERY = 'brewery';
    
    public function randomBeer()
    {
        $this->getCurrentBeer(true);
        return redirect()->route('home');
    }
    public function homepage($refresh = false)
    {
//        app('request')->session()->forget('currentBeer');
        $this->getCurrentBeer();
        return view('index');
    }
    public function sameBrewery()
    {
        $currBeer = $this->getCurrentBeer();
        $beers = false;
        
        if ($currBeer) {
            try {
                $beers = app('BreweryDbApi')->getBeersByBreweryId($currBeer['brewery']['id']);

            } catch (\Exception $e) {
                $beers = false;

                switch ($e->getCode()) {
                    case \App\BreweryDbApi\BreweryDbApiErrorCodes::ERR_BREWERY_ID : 
                        session()->flash('errorMessage', 'Brewery was not found.');
                        break;
                }
            }
        
            if ($beers) {
                // premium-glitch: the /brewery/:id/beers won't return brewery data
                // so hack it back
                $beers[0]['brewery'] = $this->getCurrentBeer()['brewery'];

                // set first one on display
                $this->setCurrentBeer($beers[0]);
                array_shift($beers);
            }
        } else {
            session()->flash('errorMessage', 'Cannot find additional beers. Please retry.');
        }
        
        return view('index', [
            'collectionType' => 'beer',
            'searchResults' => $beers
        ]);
    }
    public function searchForm(Request $request)
    {
        $this->validate($request, [
            'searchText' => ['required', 'regex:/^[-\w\s]+$/u'],
            'searchType' => ['required'], ['in' => [self::SEARCH_TYPE_BEER, self::SEARCH_TYPE_BREWERY]]
        ]);
        
        try {
            $results = app('BreweryDbApi')->search($request->get('searchText'), strtolower($request->get('searchType')));
            
        } catch (\Exception $e) {
            session()->flash('errorMessage', 'An error has occured. Please retry.');
            
            redirect()->route('home');
        }
        
        return view('index', [
            'collectionType' => $request->get('searchType'),
            'searchResults'  => $results
        ]);
    }
    private function getCurrentBeer($refreshRandom = false)
    {
        if ($refreshRandom || !session('currentBeer')) {
            try {
                $currentBeer = app('BreweryDbApi')->getRandomBeer();
            } catch (\Exception $e) {
                session()->flash('errorMessage', 'Cannot find a proper beer. Please retry.');
                $currentBeer = false;
            }
            
            $this->setCurrentBeer($currentBeer);
        } else {
            $currentBeer = session('currentBeer');
        }
        
        return $currentBeer;
    }
    private function setCurrentBeer($currentBeer)
    {
        session()->put('currentBeer', $currentBeer);
    }
}


