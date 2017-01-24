<?php
namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;


use Illuminate\Support\Facades\Log;

use Pintlabs\Service\Brewerydb;

class DashboardController extends BaseController
{
    public function randomBeer(Request $request)
    {
        $this->getCurrentBeer(true);
//        return redirect()->action('homepage', ['request' => $request, 'refresh' => true]);
        return redirect()->route('home');
//        return $this->homepage($request, true);
    }
    public function homepage(Request $request, $refresh = false)
    {
//        $request->session()->forget('currentBeer');
        
        return view('index', [
            'beer' => $this->getCurrentBeer($refresh),
        ]);
    }
    public function sameBrewery()
    {
        $currBeer = $this->getCurrentBeer();
        
        $beers = $this->getBeersByBrewery($currBeer['brewery']['id']);
        
        if ($beers) {
            $this->setCurrentBeer($beers[0]);
        }
        
        array_shift($beers);
        return view('same_brewery', [
            'collection' => $beers,
            'beer' => $this->getCurrentBeer(),
        ]);
    }
    private function getCurrentBeer($refreshRandom = false)
    {
        if ($refreshRandom || !session('currentBeer')) {
            $currentBeer = $this->getNewRandomBeer();
            
            $this->setCurrentBeer($currentBeer);
        } else {
            $currentBeer = session('currentBeer');
        }
        
        return $currentBeer;
    }
    private function setCurrentBeer($currentBeer)
    {
//        session(['currentBeer' => $currentBeer]);
        session()->put('currentBeer', $currentBeer);
    }
    private function getBeersByBrewery($breweryId)
    {
        $api = new Brewerydb(config('config.brewerydb_api_key'), config('config.brewerydb_url'));
        $api->setFormat('json');
        $result = [];
        
        try {
            $apiResults = $api->request('brewery/' . $breweryId .'/beers', [
                'withBreweries' => 'Y',
            ]);
//            $apiResults = $this->getBeersByBreweryMock();
//            Log::notice(var_export($apiResults, true));
            $beers = $apiResults['data'];

            if (!empty($beers)) {
                foreach ($beers as $b) {
                    $result[] = $this->generateBeerEntity($b);
                }
            }
            
        } catch (Exception $e) {
            Log::warning($e->getMessage());
        }
        return $result;
    }
    private function getNewRandomBeer()
    {
        $api = new Brewerydb(config('config.brewerydb_api_key'), config('config.brewerydb_url'));
        $api->setFormat('json');
        
        $randomBeer = false;
        
        try {
            $apiResults = $api->request('beers', [
                'availableId' => 1,
                'order' => 'random',
                'randomCount' => 1,
                'withBreweries' => 'Y',
            ]);
//            $apiResults = $this->returnDefault();
            
//            Log::notice(var_export($apiResults, true));
            $randomBeer = $this->generateBeerEntity($apiResults['data'][0]);
        } catch (Exception $e) {
            Log::warning($e->getMessage());
        }
        return $randomBeer;
    }
    private function generateBeerEntity($beer)
    {
        $result['name'] = false;
        $result['description'] = false;
        $result['brewery'] = false;
        $result['image'] = [
            'icon'   => asset('images/beer-glass-64.jpg'),
            'medium' => asset('images/beer-glass-256.jpg'),
            'large'  => asset('images/beer-glass-512.jpg'),
        ];
        if (isset($beer['name'])) {
            $result['name'] = $beer['name'];
        }
        if (isset($beer['description'])) {
            $result['description'] = $beer['description'];
        }
        // if there are images defined
        if (isset($beer['labels'])) {
            if (isset($beer['labels']['icon'])) {
                $result['image']['icon'] = $beer['labels']['icon'];
            }
            if (isset($beer['labels']['medium'])) {
                $result['image']['medium'] = $beer['labels']['medium'];
            }
            if (isset($beer['labels']['large'])) {
                $result['image']['large'] = $beer['labels']['large'];
            }
        }
        if (isset($beer['breweries']) && isset($beer['breweries'][0])) {
            $result['brewery'] = [
                'name' => $beer['breweries'][0]['name'],
                'id' => $beer['breweries'][0]['id'],
            ];
        }
        
        return $result;
    }
    public function someTest(Request $request)
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
    private function returnDefault()
    {
        return Array(
            'currentPage' => 1,
            'numberOfPages' => 182,
            'totalResults' => 9057,
            'data' => Array
                (
                '0' => Array
                    (
                    'id' => 'C6EUeD',
                    'name' => 'Bearpaw Brown Ale',
                    'nameDisplay' => 'Bearpaw Brown Ale',
                    'description' => 'Dark and hoppy with a balance towards malt. Our brown ale is similar to the Amber Ale but with more malt flavor.  This beer is slightly roasted with resinous and citrus hop flavors, always on tap at Altitude.',
                    'abv' => 5.7,
                    'glasswareId' => 5,
                    'availableId' => 1,
                    'styleId' => 20,
                    'isOrganic' => 'N',
//                    'labels' => Array
//                        (
//                        'icon' => 'https://s3.amazonaws.com/brewerydbapi/beer/C6EUeD/upload_OQYmDx-icon.png',
//                        'medium' => 'https://s3.amazonaws.com/brewerydbapi/beer/C6EUeD/upload_OQYmDx-medium.png',
//                        'large' => 'https://s3.amazonaws.com/brewerydbapi/beer/C6EUeD/upload_OQYmDx-large.png',
//                    ),
                    'status' => 'verified',
                    'statusDisplay' => 'Verified',
                    'createDate' => '2012-01-03 02:43:07',
                    'updateDate' => '2015-12-16 04:58:32',
                    'glass' => Array
                        (
                        'id' => 5,
                        'name' => 'Pint',
                        'createDate' => '2012-01-03 02:41:33',
                    ),
                    'available' => Array
                        (
                        'id' => 1,
                        'name' => 'Year Round',
                        'description' => 'Available year round as a staple beer.',
                    ),
                    'style' => Array
                        (
                        'id' => 20,
                        'categoryId' => 1,
                        'category' => Array
                            (
                            'id' => 1,
                            'name' => 'British Origin Ales',
                            'createDate' => '2012-03-21 20:06:45',
                        ),
                        'name' => 'Sweet or Cream Stout',
                        'shortName' => 'Sweet Stout',
                        'description' => 'Sweet stouts, also referred to as cream stouts, have less roasted bitter flavor and a full-bodied mouthfeel. The style can be given more body with milk sugar (lactose) before bottling. Malt sweetness, chocolate, and caramel flavor should dominate the flavor profile and contribute to the aroma. Hops should balance and suppress some of the sweetness without contributing apparent flavor or aroma. The overall impression should be sweet and full-bodied.',
                        'ibuMin' => 15,
                        'ibuMax' => 25,
                        'abvMin' => 3,
                        'abvMax' => 6,
                        'srmMin' => 40,
                        'srmMax' => 40,
                        'ogMin' => 1.045,
                        'fgMin' => 1.012,
                        'fgMax' => 1.02,
                        'createDate' => '2012-03-21 20:06:45',
                        'updateDate' => '2015-04-07 15:24:41',
                    ),
                ),
        ));
    }
    private function getBeersByBreweryMock()
    {
        return array (
  'message' => 'Request Successful',
  'data' => 
  array (
    0 => 
    array (
      'id' => 'ZhGbaC',
      'name' => 'Franziskaner Hefe-Weissbier',
      'nameDisplay' => 'Franziskaner Hefe-Weissbier',
      'abv' => '5',
      'glasswareId' => 9,
      'availableId' => 1,
      'styleId' => 48,
      'isOrganic' => 'N',
      'labels' => 
      array (
        'icon' => 'https://s3.amazonaws.com/brewerydbapi/beer/ZhGbaC/upload_Gzk1gj-icon.png',
        'medium' => 'https://s3.amazonaws.com/brewerydbapi/beer/ZhGbaC/upload_Gzk1gj-medium.png',
        'large' => 'https://s3.amazonaws.com/brewerydbapi/beer/ZhGbaC/upload_Gzk1gj-large.png',
      ),
      'status' => 'verified',
      'statusDisplay' => 'Verified',
      'servingTemperature' => 'cool',
      'servingTemperatureDisplay' => 'Cool - (8-12C/45-54F)',
      'createDate' => '2012-01-03 02:43:14',
      'updateDate' => '2015-12-15 21:58:34',
      'glass' => 
      array (
        'id' => 9,
        'name' => 'Weizen',
        'createDate' => '2012-01-03 02:41:33',
      ),
      'available' => 
      array (
        'id' => 1,
        'name' => 'Year Round',
        'description' => 'Available year round as a staple beer.',
      ),
      'style' => 
      array (
        'id' => 48,
        'categoryId' => 4,
        'category' => 
        array (
          'id' => 4,
          'name' => 'German Origin Ales',
          'createDate' => '2012-03-21 20:06:46',
        ),
        'name' => 'South German-Style Hefeweizen / Hefeweissbier',
        'shortName' => 'Hefeweizen',
        'description' => 'The aroma and flavor of a Weissbier with yeast is decidedly fruity and phenolic. The phenolic characteristics are often described as clove-, nutmeg-like, mildly smoke-like or even vanilla-like. Banana-like esters should be present at low to medium-high levels. These beers are made with at least 50 percent malted wheat, and hop rates are quite low. Hop flavor and aroma are absent or present at very low levels. Weissbier is well attenuated and very highly carbonated and a medium to full bodied beer. The color is very pale to pale amber. Because yeast is present, the beer will have yeast flavor and a characteristically fuller mouthfeel and may be appropriately very cloudy. No diacetyl should be perceived.',
        'ibuMin' => '10',
        'ibuMax' => '15',
        'abvMin' => '4.9',
        'abvMax' => '5.5',
        'srmMin' => '3',
        'srmMax' => '9',
        'ogMin' => '1.047',
        'fgMin' => '1.008',
        'fgMax' => '1.016',
        'createDate' => '2012-03-21 20:06:46',
        'updateDate' => '2015-04-07 15:29:27',
      ),
    ),
    1 => 
    array (
      'id' => 'tWzRix',
      'name' => 'Franziskaner Hefe-Weissbier Dunkel',
      'nameDisplay' => 'Franziskaner Hefe-Weissbier Dunkel',
      'description' => 'Franziskaner Hefe-Weisse Dunkel wins supporters with its refreshing yet aromatic and full-bodied flavour. This dark, cloudy specialty is a special treat for weiss beer connoisseurs and bock beer aficionados.

All of Franziskaner\'s weiss beer products - Hefe-Weisse Hell and Hefe-Weisse Dunkel - are top-fermentation beers noted for their agreeable carbonation levels and zesty wheat flavour. The consistently high quality of our products makes Franziskaner weiss beers a refreshing taste sensation of a special sort. All Franziskaner weiss beers are brewed in strict adherence to the Bavarian Purity Law of 1516.',
      'abv' => '5',
      'glasswareId' => 9,
      'availableId' => 1,
      'styleId' => 52,
      'isOrganic' => 'N',
      'labels' => 
      array (
        'icon' => 'https://s3.amazonaws.com/brewerydbapi/beer/tWzRix/upload_f3L9US-icon.png',
        'medium' => 'https://s3.amazonaws.com/brewerydbapi/beer/tWzRix/upload_f3L9US-medium.png',
        'large' => 'https://s3.amazonaws.com/brewerydbapi/beer/tWzRix/upload_f3L9US-large.png',
      ),
      'status' => 'verified',
      'statusDisplay' => 'Verified',
      'servingTemperature' => 'cool',
      'servingTemperatureDisplay' => 'Cool - (8-12C/45-54F)',
      'createDate' => '2012-01-03 02:43:14',
      'updateDate' => '2016-03-21 19:56:20',
      'glass' => 
      array (
        'id' => 9,
        'name' => 'Weizen',
        'createDate' => '2012-01-03 02:41:33',
      ),
      'available' => 
      array (
        'id' => 1,
        'name' => 'Year Round',
        'description' => 'Available year round as a staple beer.',
      ),
      'style' => 
      array (
        'id' => 52,
        'categoryId' => 4,
        'category' => 
        array (
          'id' => 4,
          'name' => 'German Origin Ales',
          'createDate' => '2012-03-21 20:06:46',
        ),
        'name' => 'South German-Style Dunkel Weizen / Dunkel Weissbier',
        'shortName' => 'Dunkelweizen',
        'description' => 'This beer style is characterized by a distinct sweet maltiness and a chocolate-like character from roasted malt. Estery and phenolic elements of this Weissbier should be evident but subdued. Color can range from copper-brown to dark brown. Dunkel Weissbier is well attenuated and very highly carbonated, and hop bitterness is low. Hop flavor and aroma are absent. Usually dark barley malts are used in conjunction with dark cara or color malts, and the percentage of wheat malt is at least 50 percent. If this is served with yeast, the beer may be appropriately very cloudy. No diacetyl should be perceived.',
        'ibuMin' => '10',
        'ibuMax' => '15',
        'abvMin' => '4.8',
        'abvMax' => '5.4',
        'srmMin' => '10',
        'srmMax' => '19',
        'ogMin' => '1.048',
        'fgMin' => '1.008',
        'fgMax' => '1.016',
        'createDate' => '2012-03-21 20:06:46',
        'updateDate' => '2015-04-07 15:30:09',
      ),
    ),
    2 => 
    array (
      'id' => '6YgqZ7',
      'name' => 'Franziskaner Weissbier Alkoholfrei',
      'nameDisplay' => 'Franziskaner Weissbier Alkoholfrei',
      'description' => 'This specialty stands out because of its aromatic and fresh Franziskaner Weissbier taste. Rich in vitamins and trace elements, this Weissbier is excellently suited as an isotonic thirst quencher. Brewed in accordance with the Bavarian Reinheitsgebot of 1516.',
      'abv' => '0.5',
      'glasswareId' => 9,
      'availableId' => 1,
      'styleId' => 77,
      'isOrganic' => 'N',
      'labels' => 
      array (
        'icon' => 'https://s3.amazonaws.com/brewerydbapi/beer/6YgqZ7/upload_0bQwv6-icon.png',
        'medium' => 'https://s3.amazonaws.com/brewerydbapi/beer/6YgqZ7/upload_0bQwv6-medium.png',
        'large' => 'https://s3.amazonaws.com/brewerydbapi/beer/6YgqZ7/upload_0bQwv6-large.png',
      ),
      'status' => 'verified',
      'statusDisplay' => 'Verified',
      'servingTemperature' => 'cool',
      'servingTemperatureDisplay' => 'Cool - (8-12C/45-54F)',
      'createDate' => '2014-05-07 16:36:10',
      'updateDate' => '2015-12-17 12:12:32',
      'glass' => 
      array (
        'id' => 9,
        'name' => 'Weizen',
        'createDate' => '2012-01-03 02:41:33',
      ),
      'available' => 
      array (
        'id' => 1,
        'name' => 'Year Round',
        'description' => 'Available year round as a staple beer.',
      ),
      'style' => 
      array (
        'id' => 77,
        'categoryId' => 7,
        'category' => 
        array (
          'id' => 7,
          'name' => 'European-germanic Lager',
          'createDate' => '2012-03-21 20:06:46',
        ),
        'name' => 'German-Style Leichtbier',
        'shortName' => 'Leichtbier',
        'description' => 'These beers are very light in body and color. Malt sweetness is perceived at low to medium levels, while hop bitterness character is perceived at medium levels. Hop flavor and aroma may be low to medium. These beers should be clean with no perceived fruity esters or diacetyl. Very low levels of sulfur related compounds acceptable. Chill haze is not acceptable.',
        'ibuMin' => '16',
        'ibuMax' => '24',
        'abvMin' => '2.5',
        'abvMax' => '3.6',
        'srmMin' => '2',
        'srmMax' => '4',
        'ogMin' => '1.026',
        'fgMin' => '1.006',
        'fgMax' => '1.01',
        'createDate' => '2012-03-21 20:06:46',
        'updateDate' => '2015-04-07 17:14:37',
      ),
    ),
    3 => 
    array (
      'id' => 'INTvJB',
      'name' => 'Franziskaner Weissbier alkoholfrei Holunder',
      'nameDisplay' => 'Franziskaner Weissbier alkoholfrei Holunder',
      'availableId' => 1,
      'styleId' => 49,
      'isOrganic' => 'N',
      'status' => 'verified',
      'statusDisplay' => 'Verified',
      'createDate' => '2015-11-28 19:06:05',
      'updateDate' => '2015-12-02 19:31:30',
      'available' => 
      array (
        'id' => 1,
        'name' => 'Year Round',
        'description' => 'Available year round as a staple beer.',
      ),
      'style' => 
      array (
        'id' => 49,
        'categoryId' => 4,
        'category' => 
        array (
          'id' => 4,
          'name' => 'German Origin Ales',
          'createDate' => '2012-03-21 20:06:46',
        ),
        'name' => 'South German-Style Kristall Weizen / Kristall Weissbier',
        'shortName' => 'Kristallweizen',
        'description' => 'The aroma and flavor of a Weissbier without yeast is very similar to Weissbier with yeast (Hefeweizen/Hefeweissbier) with the caveat that fruity and phenolic characters are not combined with the yeasty flavor and fuller-bodied mouthfeel of yeast. The phenolic characteristics are often described as clove- or nutmeg-like and can be smoky or even vanilla-like. Banana-like esters are often present. These beers are made with at least 50 percent malted wheat, and hop rates are quite low. Hop flavor and aroma are absent. Weissbier is well attenuated and very highly carbonated, yet its relatively high starting gravity and alcohol content make it a medium- to full-bodied beer. The color is very pale to deep golden. Because the beer has been filtered, yeast is not present. The beer will have no flavor of yeast and a cleaner, drier mouthfeel. The beer should be clear with no chill haze present. No diacetyl should be perceived.',
        'ibuMin' => '10',
        'ibuMax' => '15',
        'abvMin' => '4.9',
        'abvMax' => '5.5',
        'srmMin' => '3',
        'srmMax' => '9',
        'ogMin' => '1.047',
        'fgMin' => '1.008',
        'fgMax' => '1.016',
        'createDate' => '2012-03-21 20:06:46',
        'updateDate' => '2015-04-07 15:29:35',
      ),
    ),
    4 => 
    array (
      'id' => 'g3l79k',
      'name' => 'Franziskaner Weissbier alkoholfrei Zitrone',
      'nameDisplay' => 'Franziskaner Weissbier alkoholfrei Zitrone',
      'availableId' => 1,
      'styleId' => 49,
      'isOrganic' => 'N',
      'status' => 'verified',
      'statusDisplay' => 'Verified',
      'createDate' => '2015-11-28 19:05:07',
      'updateDate' => '2015-12-02 19:31:29',
      'available' => 
      array (
        'id' => 1,
        'name' => 'Year Round',
        'description' => 'Available year round as a staple beer.',
      ),
      'style' => 
      array (
        'id' => 49,
        'categoryId' => 4,
        'category' => 
        array (
          'id' => 4,
          'name' => 'German Origin Ales',
          'createDate' => '2012-03-21 20:06:46',
        ),
        'name' => 'South German-Style Kristall Weizen / Kristall Weissbier',
        'shortName' => 'Kristallweizen',
        'description' => 'The aroma and flavor of a Weissbier without yeast is very similar to Weissbier with yeast (Hefeweizen/Hefeweissbier) with the caveat that fruity and phenolic characters are not combined with the yeasty flavor and fuller-bodied mouthfeel of yeast. The phenolic characteristics are often described as clove- or nutmeg-like and can be smoky or even vanilla-like. Banana-like esters are often present. These beers are made with at least 50 percent malted wheat, and hop rates are quite low. Hop flavor and aroma are absent. Weissbier is well attenuated and very highly carbonated, yet its relatively high starting gravity and alcohol content make it a medium- to full-bodied beer. The color is very pale to deep golden. Because the beer has been filtered, yeast is not present. The beer will have no flavor of yeast and a cleaner, drier mouthfeel. The beer should be clear with no chill haze present. No diacetyl should be perceived.',
        'ibuMin' => '10',
        'ibuMax' => '15',
        'abvMin' => '4.9',
        'abvMax' => '5.5',
        'srmMin' => '3',
        'srmMax' => '9',
        'ogMin' => '1.047',
        'fgMin' => '1.008',
        'fgMax' => '1.016',
        'createDate' => '2012-03-21 20:06:46',
        'updateDate' => '2015-04-07 15:29:35',
      ),
    ),
    5 => 
    array (
      'id' => 'UEy2Pg',
      'name' => 'Franziskaner Weissbier Kristallklar',
      'nameDisplay' => 'Franziskaner Weissbier Kristallklar',
      'description' => 'Hops from Munich; Franziskaner top fermenting yeast strain; Water from own wells 200m deep.
Crystal clear, clean in taste and color. For those who desire a weissbier without yeast. 
Also known as Club Weissbier and as Kristall Klar.',
      'abv' => '5',
      'glasswareId' => 9,
      'availableId' => 1,
      'styleId' => 49,
      'isOrganic' => 'N',
      'labels' => 
      array (
        'icon' => 'https://s3.amazonaws.com/brewerydbapi/beer/UEy2Pg/upload_WjgnpS-icon.png',
        'medium' => 'https://s3.amazonaws.com/brewerydbapi/beer/UEy2Pg/upload_WjgnpS-medium.png',
        'large' => 'https://s3.amazonaws.com/brewerydbapi/beer/UEy2Pg/upload_WjgnpS-large.png',
      ),
      'status' => 'verified',
      'statusDisplay' => 'Verified',
      'servingTemperature' => 'cool',
      'servingTemperatureDisplay' => 'Cool - (8-12C/45-54F)',
      'createDate' => '2014-05-07 16:33:44',
      'updateDate' => '2015-12-17 13:25:59',
      'glass' => 
      array (
        'id' => 9,
        'name' => 'Weizen',
        'createDate' => '2012-01-03 02:41:33',
      ),
      'available' => 
      array (
        'id' => 1,
        'name' => 'Year Round',
        'description' => 'Available year round as a staple beer.',
      ),
      'style' => 
      array (
        'id' => 49,
        'categoryId' => 4,
        'category' => 
        array (
          'id' => 4,
          'name' => 'German Origin Ales',
          'createDate' => '2012-03-21 20:06:46',
        ),
        'name' => 'South German-Style Kristall Weizen / Kristall Weissbier',
        'shortName' => 'Kristallweizen',
        'description' => 'The aroma and flavor of a Weissbier without yeast is very similar to Weissbier with yeast (Hefeweizen/Hefeweissbier) with the caveat that fruity and phenolic characters are not combined with the yeasty flavor and fuller-bodied mouthfeel of yeast. The phenolic characteristics are often described as clove- or nutmeg-like and can be smoky or even vanilla-like. Banana-like esters are often present. These beers are made with at least 50 percent malted wheat, and hop rates are quite low. Hop flavor and aroma are absent. Weissbier is well attenuated and very highly carbonated, yet its relatively high starting gravity and alcohol content make it a medium- to full-bodied beer. The color is very pale to deep golden. Because the beer has been filtered, yeast is not present. The beer will have no flavor of yeast and a cleaner, drier mouthfeel. The beer should be clear with no chill haze present. No diacetyl should be perceived.',
        'ibuMin' => '10',
        'ibuMax' => '15',
        'abvMin' => '4.9',
        'abvMax' => '5.5',
        'srmMin' => '3',
        'srmMax' => '9',
        'ogMin' => '1.047',
        'fgMin' => '1.008',
        'fgMax' => '1.016',
        'createDate' => '2012-03-21 20:06:46',
        'updateDate' => '2015-04-07 15:29:35',
      ),
    ),
    6 => 
    array (
      'id' => 'isPeiY',
      'name' => 'Franziskaner Weissbier Leicht',
      'nameDisplay' => 'Franziskaner Weissbier Leicht',
      'description' => 'This specialty from Franziskaner Weissbier has a full-bodied, spicy taste and is distinctively lively. It is low in calories and alcohol and therefore the ideal alternative for physically active beer consumers, who carefully watch their nutrition. Brewed in accordance with the Bavarian Reinheitsgebot of 1516.',
      'abv' => '2.9',
      'glasswareId' => 9,
      'availableId' => 1,
      'styleId' => 48,
      'isOrganic' => 'N',
      'status' => 'verified',
      'statusDisplay' => 'Verified',
      'servingTemperature' => 'cold',
      'servingTemperatureDisplay' => 'Cold - (4-7C/39-45F)',
      'createDate' => '2014-05-07 16:49:12',
      'updateDate' => '2015-01-07 13:19:08',
      'glass' => 
      array (
        'id' => 9,
        'name' => 'Weizen',
        'createDate' => '2012-01-03 02:41:33',
      ),
      'available' => 
      array (
        'id' => 1,
        'name' => 'Year Round',
        'description' => 'Available year round as a staple beer.',
      ),
      'style' => 
      array (
        'id' => 48,
        'categoryId' => 4,
        'category' => 
        array (
          'id' => 4,
          'name' => 'German Origin Ales',
          'createDate' => '2012-03-21 20:06:46',
        ),
        'name' => 'South German-Style Hefeweizen / Hefeweissbier',
        'shortName' => 'Hefeweizen',
        'description' => 'The aroma and flavor of a Weissbier with yeast is decidedly fruity and phenolic. The phenolic characteristics are often described as clove-, nutmeg-like, mildly smoke-like or even vanilla-like. Banana-like esters should be present at low to medium-high levels. These beers are made with at least 50 percent malted wheat, and hop rates are quite low. Hop flavor and aroma are absent or present at very low levels. Weissbier is well attenuated and very highly carbonated and a medium to full bodied beer. The color is very pale to pale amber. Because yeast is present, the beer will have yeast flavor and a characteristically fuller mouthfeel and may be appropriately very cloudy. No diacetyl should be perceived.',
        'ibuMin' => '10',
        'ibuMax' => '15',
        'abvMin' => '4.9',
        'abvMax' => '5.5',
        'srmMin' => '3',
        'srmMax' => '9',
        'ogMin' => '1.047',
        'fgMin' => '1.008',
        'fgMax' => '1.016',
        'createDate' => '2012-03-21 20:06:46',
        'updateDate' => '2015-04-07 15:29:27',
      ),
    ),
    7 => 
    array (
      'id' => 'lmxSPr',
      'name' => 'Munchner Hell',
      'nameDisplay' => 'Munchner Hell',
      'abv' => '5.2',
      'styleId' => 78,
      'isOrganic' => 'N',
      'status' => 'verified',
      'statusDisplay' => 'Verified',
      'createDate' => '2016-02-18 14:56:00',
      'updateDate' => '2016-02-18 14:56:00',
      'style' => 
      array (
        'id' => 78,
        'categoryId' => 7,
        'category' => 
        array (
          'id' => 7,
          'name' => 'European-germanic Lager',
          'createDate' => '2012-03-21 20:06:46',
        ),
        'name' => 'Münchner (Munich)-Style Helles',
        'shortName' => 'Helles',
        'description' => 'This beer should be perceived as having low bitterness. It is a medium-bodied, malt-emphasized beer with malt character often balanced with low levels of yeast produced sulfur compounds (character). Certain renditions of this beer style have  a perceivable level of hop flavor (note: hop flavor does not imply hop bitterness) and character but it is essentially balanced with malt character to retain its style identity. Malt character is sometimes bread-like yet always reminiscent of freshly and very lightly toasted malted barley. There should not be any caramel character. Color is light straw to golden. Fruity esters and diacetyl should not be perceived. There should be no chill haze.',
        'ibuMin' => '18',
        'ibuMax' => '25',
        'abvMin' => '4.5',
        'abvMax' => '5.5',
        'srmMin' => '4',
        'srmMax' => '6',
        'ogMin' => '1.044',
        'fgMin' => '1.008',
        'fgMax' => '1.012',
        'createDate' => '2012-03-21 20:06:46',
        'updateDate' => '2015-04-07 15:36:40',
      ),
    ),
    8 => 
    array (
      'id' => '7ZBrpD',
      'name' => 'Spaten Alkoholfrei',
      'nameDisplay' => 'Spaten Alkoholfrei',
      'description' => 'Enjoyment of beer Enjoyment of beer without alcohol; healthy, isotonic drink not only for athletes.',
      'glasswareId' => 1,
      'availableId' => 1,
      'styleId' => 77,
      'isOrganic' => 'N',
      'labels' => 
      array (
        'icon' => 'https://s3.amazonaws.com/brewerydbapi/beer/7ZBrpD/upload_uKchZ5-icon.png',
        'medium' => 'https://s3.amazonaws.com/brewerydbapi/beer/7ZBrpD/upload_uKchZ5-medium.png',
        'large' => 'https://s3.amazonaws.com/brewerydbapi/beer/7ZBrpD/upload_uKchZ5-large.png',
      ),
      'status' => 'verified',
      'statusDisplay' => 'Verified',
      'servingTemperature' => 'cold',
      'servingTemperatureDisplay' => 'Cold - (4-7C/39-45F)',
      'createDate' => '2014-05-02 10:35:25',
      'updateDate' => '2015-12-17 12:31:59',
      'glass' => 
      array (
        'id' => 1,
        'name' => 'Flute',
        'createDate' => '2012-01-03 02:41:33',
      ),
      'available' => 
      array (
        'id' => 1,
        'name' => 'Year Round',
        'description' => 'Available year round as a staple beer.',
      ),
      'style' => 
      array (
        'id' => 77,
        'categoryId' => 7,
        'category' => 
        array (
          'id' => 7,
          'name' => 'European-germanic Lager',
          'createDate' => '2012-03-21 20:06:46',
        ),
        'name' => 'German-Style Leichtbier',
        'shortName' => 'Leichtbier',
        'description' => 'These beers are very light in body and color. Malt sweetness is perceived at low to medium levels, while hop bitterness character is perceived at medium levels. Hop flavor and aroma may be low to medium. These beers should be clean with no perceived fruity esters or diacetyl. Very low levels of sulfur related compounds acceptable. Chill haze is not acceptable.',
        'ibuMin' => '16',
        'ibuMax' => '24',
        'abvMin' => '2.5',
        'abvMax' => '3.6',
        'srmMin' => '2',
        'srmMax' => '4',
        'ogMin' => '1.026',
        'fgMin' => '1.006',
        'fgMax' => '1.01',
        'createDate' => '2012-03-21 20:06:46',
        'updateDate' => '2015-04-07 17:14:37',
      ),
    ),
    9 => 
    array (
      'id' => '83WNK4',
      'name' => 'Spaten Maibock',
      'nameDisplay' => 'Spaten Maibock',
      'description' => 'Spaten may be more famous for its Optimator doppelbock, but its helles bock is no less stellar. Surprisingly pale in the glass, it has a long-lasting crown. The aroma is full and malty, with a fresh but soft hop aroma. The palate is full but somewhat crisp, and rich with pale malt character. The hops serve only to hold up the malt. With 600 years of brewing behind it, Spaten never seems to disappoint. This is a classic German blond bock, suitable for any time of the year.',
      'abv' => '6.5',
      'glasswareId' => 3,
      'availableId' => 1,
      'styleId' => 3,
      'isOrganic' => 'N',
      'status' => 'verified',
      'statusDisplay' => 'Verified',
      'servingTemperature' => 'cold',
      'servingTemperatureDisplay' => 'Cold - (4-7C/39-45F)',
      'createDate' => '2012-01-03 02:43:39',
      'updateDate' => '2014-05-11 13:38:13',
      'glass' => 
      array (
        'id' => 3,
        'name' => 'Mug',
        'createDate' => '2012-01-03 02:41:33',
      ),
      'available' => 
      array (
        'id' => 1,
        'name' => 'Year Round',
        'description' => 'Available year round as a staple beer.',
      ),
      'style' => 
      array (
        'id' => 3,
        'categoryId' => 1,
        'category' => 
        array (
          'id' => 1,
          'name' => 'British Origin Ales',
          'createDate' => '2012-03-21 20:06:45',
        ),
        'name' => 'Ordinary Bitter',
        'shortName' => 'Bitter',
        'description' => 'Ordinary bitter is gold to copper colored with medium bitterness, light to medium body, and low to medium residual malt sweetness. Hop flavor and aroma character may be evident at the brewer\'s discretion. Mild carbonation traditionally characterizes draft-cask versions, but in bottled versions, a slight increase in carbon dioxide content is acceptable. Fruity-ester character and very low diacetyl (butterscotch) character are acceptable in aroma and flavor, but should be minimized in this form of bitter. Chill haze is allowable at cold temperatures. (English and American hop character may be specified in subcategories.)',
        'ibuMin' => '20',
        'ibuMax' => '35',
        'abvMin' => '3',
        'abvMax' => '4.1',
        'srmMin' => '5',
        'srmMax' => '12',
        'ogMin' => '1.033',
        'fgMin' => '1.006',
        'fgMax' => '1.012',
        'createDate' => '2012-03-21 20:06:45',
        'updateDate' => '2015-04-07 15:18:39',
      ),
    ),
    10 => 
    array (
      'id' => '673NQh',
      'name' => 'Spaten Oktoberfestbier',
      'nameDisplay' => 'Spaten Oktoberfestbier',
      'description' => 'Our Oktoberfest Beer, created in 1872, is the world\'s first Oktoberfest beer, brewed for the greatest folk festival in the world. Every year, over and over again, countless Oktoberfest visitors share their enthusiasm about this beer.

Flavor profile: Amber in color. This medium bodied beer has achieved its impeccable taste by balancing the roasted malt flavor with the perfect amount of hops. Having a rich textured palate with an underlying sweetness true to tradition.',
      'abv' => '5.9',
      'glasswareId' => 3,
      'availableId' => 4,
      'styleId' => 81,
      'isOrganic' => 'N',
      'labels' => 
      array (
        'icon' => 'https://s3.amazonaws.com/brewerydbapi/beer/673NQh/upload_uJqsSI-icon.png',
        'medium' => 'https://s3.amazonaws.com/brewerydbapi/beer/673NQh/upload_uJqsSI-medium.png',
        'large' => 'https://s3.amazonaws.com/brewerydbapi/beer/673NQh/upload_uJqsSI-large.png',
      ),
      'status' => 'verified',
      'statusDisplay' => 'Verified',
      'servingTemperature' => 'cold',
      'servingTemperatureDisplay' => 'Cold - (4-7C/39-45F)',
      'createDate' => '2012-01-03 02:43:49',
      'updateDate' => '2016-09-30 17:19:00',
      'glass' => 
      array (
        'id' => 3,
        'name' => 'Mug',
        'createDate' => '2012-01-03 02:41:33',
      ),
      'available' => 
      array (
        'id' => 4,
        'name' => 'Seasonal',
        'description' => 'Available at the same time of year, every year.',
      ),
      'style' => 
      array (
        'id' => 81,
        'categoryId' => 7,
        'category' => 
        array (
          'id' => 7,
          'name' => 'European-germanic Lager',
          'createDate' => '2012-03-21 20:06:46',
        ),
        'name' => 'German-Style Märzen',
        'shortName' => 'Märzen',
        'description' => 'Märzens are characterized by a medium body and broad range of color. They can range from golden to reddish orange. Sweet maltiness should dominate slightly over a clean hop bitterness. Malt character should be light-toasted rather than strongly caramel (though a low level of light caramel character is acceptable). Bread or biscuit-like malt character is acceptable in aroma and flavor. Hop aroma and flavor should be low but notable. Ale-like fruity esters should not be perceived. Diacetyl and chill haze should not be perceived.',
        'ibuMin' => '18',
        'ibuMax' => '25',
        'abvMin' => '5.3',
        'abvMax' => '5.9',
        'srmMin' => '4',
        'srmMax' => '15',
        'ogMin' => '1.05',
        'fgMin' => '1.012',
        'fgMax' => '1.02',
        'createDate' => '2012-03-21 20:06:46',
        'updateDate' => '2015-04-07 15:36:50',
      ),
    ),
    11 => 
    array (
      'id' => 'j2wNuw',
      'name' => 'Spaten Optimator',
      'nameDisplay' => 'Spaten Optimator',
      'abv' => '7.2',
      'glasswareId' => 5,
      'availableId' => 1,
      'styleId' => 90,
      'isOrganic' => 'N',
      'labels' => 
      array (
        'icon' => 'https://s3.amazonaws.com/brewerydbapi/beer/j2wNuw/upload_Sd80Bu-icon.png',
        'medium' => 'https://s3.amazonaws.com/brewerydbapi/beer/j2wNuw/upload_Sd80Bu-medium.png',
        'large' => 'https://s3.amazonaws.com/brewerydbapi/beer/j2wNuw/upload_Sd80Bu-large.png',
      ),
      'status' => 'verified',
      'statusDisplay' => 'Verified',
      'servingTemperature' => 'cool',
      'servingTemperatureDisplay' => 'Cool - (8-12C/45-54F)',
      'createDate' => '2012-01-03 02:43:52',
      'updateDate' => '2015-12-16 03:36:57',
      'glass' => 
      array (
        'id' => 5,
        'name' => 'Pint',
        'createDate' => '2012-01-03 02:41:33',
      ),
      'available' => 
      array (
        'id' => 1,
        'name' => 'Year Round',
        'description' => 'Available year round as a staple beer.',
      ),
      'style' => 
      array (
        'id' => 90,
        'categoryId' => 7,
        'category' => 
        array (
          'id' => 7,
          'name' => 'European-germanic Lager',
          'createDate' => '2012-03-21 20:06:46',
        ),
        'name' => 'German-Style Doppelbock',
        'shortName' => 'Doppelbock',
        'description' => 'Malty sweetness is dominant but should not be cloying. Malt character is more reminiscent of fresh and lightly toasted Munich- style malt, more so than caramel or toffee malt character. Some elements of caramel and toffee can be evident and contribute to complexity, but the predominant malt character is an expression of toasted barley malt. Doppelbocks are full bodied and deep amber to dark brown in color. Astringency from roast malts is absent. Alcoholic strength is high, and hop rates increase with gravity. Hop bitterness and flavor should be low and hop aroma absent. Fruity esters are commonly perceived but at low to moderate levels. Diacetyl should be absent',
        'ibuMin' => '17',
        'ibuMax' => '27',
        'abvMin' => '6.5',
        'abvMax' => '8',
        'srmMin' => '12',
        'srmMax' => '30',
        'ogMin' => '1.074',
        'fgMin' => '1.014',
        'fgMax' => '1.02',
        'createDate' => '2012-03-21 20:06:46',
        'updateDate' => '2015-04-07 15:39:08',
      ),
    ),
    12 => 
    array (
      'id' => 'wfAwfx',
      'name' => 'Spaten Pils',
      'nameDisplay' => 'Spaten Pils',
      'description' => 'The classic Bavarian pilsner, bottom fermented.',
      'abv' => '5',
      'glasswareId' => 4,
      'availableId' => 1,
      'styleId' => 75,
      'isOrganic' => 'N',
      'labels' => 
      array (
        'icon' => 'https://s3.amazonaws.com/brewerydbapi/beer/wfAwfx/upload_QFVk3n-icon.png',
        'medium' => 'https://s3.amazonaws.com/brewerydbapi/beer/wfAwfx/upload_QFVk3n-medium.png',
        'large' => 'https://s3.amazonaws.com/brewerydbapi/beer/wfAwfx/upload_QFVk3n-large.png',
      ),
      'status' => 'verified',
      'statusDisplay' => 'Verified',
      'servingTemperature' => 'cold',
      'servingTemperatureDisplay' => 'Cold - (4-7C/39-45F)',
      'createDate' => '2012-01-03 02:43:57',
      'updateDate' => '2015-12-15 21:05:32',
      'glass' => 
      array (
        'id' => 4,
        'name' => 'Pilsner',
        'createDate' => '2012-01-03 02:41:33',
      ),
      'available' => 
      array (
        'id' => 1,
        'name' => 'Year Round',
        'description' => 'Available year round as a staple beer.',
      ),
      'style' => 
      array (
        'id' => 75,
        'categoryId' => 7,
        'category' => 
        array (
          'id' => 7,
          'name' => 'European-germanic Lager',
          'createDate' => '2012-03-21 20:06:46',
        ),
        'name' => 'German-Style Pilsener',
        'shortName' => 'German Pilsener',
        'description' => 'A classic German Pilsener is very light straw or golden in color and well hopped. Perception of hop bitterness is medium to high. Noble-type hop aroma and flavor are moderate and quite obvious. It is a well-attenuated, medium-light bodied beer, but a malty residual sweetness can be perceived in aroma and flavor. Very low levels of sweet corn-like dimethylsulfide (DMS) character are below most beer drinkers\' taste thresholds and are usually not detectable except to the trained or sensitive palate. Other fermentation or hop related sulfur compounds, when perceived at low levels, may be characteristic of this style. Fruity esters and diacetyl should not be perceived. There should be no chill haze. Its head should be dense and rich.',
        'ibuMin' => '25',
        'ibuMax' => '40',
        'abvMin' => '4',
        'abvMax' => '5',
        'srmMin' => '3',
        'srmMax' => '4',
        'ogMin' => '1.044',
        'fgMin' => '1.006',
        'fgMax' => '1.012',
        'createDate' => '2012-03-21 20:06:46',
        'updateDate' => '2015-04-07 15:35:59',
      ),
    ),
    13 => 
    array (
      'id' => 'FTCwW9',
      'name' => 'Spaten Premium Lager',
      'nameDisplay' => 'Spaten Premium Lager',
      'description' => 'This beer is our speciality. In 1894 Spaten became the first brewery in Munich to produce this brand of light lager.

Flavor profile: Golden in color with a well-balanced hop-flavor. The full rounded body is a superb balance between hops and a malty sweetness.',
      'abv' => '5.2',
      'glasswareId' => 5,
      'availableId' => 1,
      'styleId' => 97,
      'isOrganic' => 'N',
      'labels' => 
      array (
        'icon' => 'https://s3.amazonaws.com/brewerydbapi/beer/FTCwW9/upload_n0Htyy-icon.png',
        'medium' => 'https://s3.amazonaws.com/brewerydbapi/beer/FTCwW9/upload_n0Htyy-medium.png',
        'large' => 'https://s3.amazonaws.com/brewerydbapi/beer/FTCwW9/upload_n0Htyy-large.png',
      ),
      'status' => 'verified',
      'statusDisplay' => 'Verified',
      'servingTemperature' => 'cold',
      'servingTemperatureDisplay' => 'Cold - (4-7C/39-45F)',
      'createDate' => '2012-01-03 02:44:00',
      'updateDate' => '2015-12-18 14:42:54',
      'glass' => 
      array (
        'id' => 5,
        'name' => 'Pint',
        'createDate' => '2012-01-03 02:41:33',
      ),
      'available' => 
      array (
        'id' => 1,
        'name' => 'Year Round',
        'description' => 'Available year round as a staple beer.',
      ),
      'style' => 
      array (
        'id' => 97,
        'categoryId' => 8,
        'category' => 
        array (
          'id' => 8,
          'name' => 'North American Lager',
          'createDate' => '2012-03-21 20:06:46',
        ),
        'name' => 'American-Style Premium Lager',
        'shortName' => 'American Premium Lager',
        'description' => 'This style has low malt (and adjunct) sweetness, is medium bodied, and should contain no or a low percentage (less than 25%) of adjuncts. Color may be light straw to golden. Alcohol content and bitterness may also be greater. Hop aroma and flavor is low or negligible. Light fruity esters are acceptable. Chill haze and diacetyl should be absent. Note: Some beers marketed as "premium" (based on price) may not fit this definition.',
        'ibuMin' => '6',
        'ibuMax' => '15',
        'abvMin' => '4.3',
        'abvMax' => '5',
        'srmMin' => '2',
        'srmMax' => '6',
        'ogMin' => '1.044',
        'fgMin' => '1.01',
        'fgMax' => '1.014',
        'createDate' => '2012-03-21 20:06:46',
        'updateDate' => '2015-04-07 15:40:04',
      ),
    ),
  ),
  'status' => 'success',
);
    }
}


