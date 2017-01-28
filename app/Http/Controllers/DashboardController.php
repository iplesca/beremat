<?php
namespace App\Http\Controllers;

use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use App\BreweryDbApi\BreweryDbApi;
use App\BeerDispatch;
use App\SearchDispatch;

class DashboardController extends Controller
{
    use ValidatesRequests;
    
    /**
     * Fetches a new random $currentBeer. 
     * Redirect to home (to prevent accidental refresh -> API call)
     * 
     * @return \Illuminate\Routing\Redirector
     */
    public function randomBeer()
    {
        $this->getCurrentBeer(true);
        return redirect()->route('home');
    }
    /**
     * Generates currentBeer.
     * Displays the index (home) page.
     * 
     * @param bool $refresh Whether to refresh the $currentBeer in session
     * @return \Illuminate\View\View
     */
    public function homepage($refresh = false)
    {
//        app('request')->session()->forget('errorMessage');
//        app('request')->session()->forget('currentBeer');
        $this->getCurrentBeer();
        return view('index');
    }
    /**
     * Fetches more beer records using the $currentBeer brewery id.
     * Display them as search results.
     * 
     * @return \Illuminate\View\View
     */
    public function sameBrewery()
    {
        $currBeer = $this->getCurrentBeer();
        $beers = false;
        
        if ($currBeer) {
            $bd = new BeerDispatch();
            $beers = $bd->getBeersFromSameBrewery($currBeer);
            
            if ($beers) {
                // set first one on display
                $this->setCurrentBeer($beers[0]);
                array_shift($beers);
                
                // ensure the search results have the appropriate structure
                $beers = SearchDispatch::generateResultsFromBeerCollection($beers);
            }
            
            
        } else {
            session()->flash('errorMessage', 'Cannot find additional beers. Please retry.');
        }
        
        return view('index', [
            'searchResults' => $beers
        ]);
    }
    /**
     * Searches for records using a text pattern and a type (beer/brewery)
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function searchForm(Request $request)
    {
        // this will bail out of errors
        $this->validate($request, [
            'searchText' => ['required', 'regex:/^[-\w\s]+$/u'],
            'searchType' => ['required'], ['in' => [SearchDispatch::SEARCH_TYPE_BEER, SearchDispatch::SEARCH_TYPE_BREWERY]]
        ]);
        
        $sd = new SearchDispatch();
        $result = $sd->search($request->get('searchText'), strtolower($request->get('searchType')));
        
        
        if (!$result) {
            // 'cos mama i'm coooming hoomee...
            redirect()->route('home');
        }
        
        return view('index', [
            'searchResults'  => $result
        ]);
    }
    /**
     * Generates a new $currentBeer ($refreshRandom on demand) and stores it in session
     * Returns the session $currentBeer
     * 
     * @param bool $refreshRandom
     * @return array
     */
    private function getCurrentBeer($refreshRandom = false)
    {
        if ($refreshRandom || !session('currentBeer')) {
            $bd = new BeerDispatch();
            $currentBeer = $bd->getRandomBeer();

            if (false === $currentBeer) {
                session()->flash('errorMessage', 'Cannot find a proper beer. Please retry.');
            } else {
                $this->setCurrentBeer($currentBeer);
            }
        } else {
            $currentBeer = session('currentBeer');
        }
        return $currentBeer;
    }
    /**
     * Replace the session $currentBeer with a new array of App\Beer instance
     * 
     * @param  $currentBeer
     */
    private function setCurrentBeer($currentBeer)
    {
        session()->put('currentBeer', $currentBeer);
    }
}


