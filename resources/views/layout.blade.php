<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@yield('title')</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        <!-- Fonts -->
        <!--<link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">-->
    </head>
    <body>
<!--        <div class="col-md-12">
            @yield('content')
        </div>-->

        <div class="container">
            <div class="row">
            <h1>Beer-mat</h1>
                <div class="col-lg-1">
                    <img class="" src="https://s3.amazonaws.com/brewerydbapi/beer/C6EUeD/upload_OQYmDx-medium.png">
                </div>
                <div class="col-lg-offset-2 col-lg-6">
                    <div class="row">
                        <div class="col-lg-12"><h3>Duck-Rabbit Milk Stout</h3></div>
                        <div class="col-lg-12">The Duck-Rabbit Milk Stout is a traditional full-bodied stout brewed with lactose (milk sugar). The subtle sweetness imparted by the lactose balances the sharpness of the highly roasted grains which give this delicious beer its black color.</div>
                    </div>
                </div>
                <div class="col-lg-2">
                    <button class="btn btn-primary btn-lg" type="submit">Another beer</button>
                    <button class="btn btn-info btn-lg" type="submit">More from this brewery</button>
                </div>
            </div>
            <div class="row">
                <h4>Plenty of things to look for ...</h4>
                <form class="form-horizontal form-inline" method="post" action="#">
                    <div class="col-lg-3 form-group">
                        <input class="form-control input-lg" type="text" placeholder="Search">
                    </div>
                    <div class="col-lg-2 form-group">
                        <label class="radio-inline">
                            <input type="radio" name="searchType" value="beer" id="searchTypeBeer">Beer
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="searchType" value="brewery" id="searchTypeBrewery">Brewery
                        </label>
                    </div>
                    <div class="form-group col-lg-1">
                        <button class="btn btn-lg btn-info" id="doSearch">Find</button>
                    </div>
                </form>
            </div>
            <div class="row">
                <h4><strong>Search results</strong></h4>
                <div class="col-lg-12">
                    <div class="row search-result-row">
                        <div class="col-lg-1">
                            <img src="https://s3.amazonaws.com/brewerydbapi/beer/C6EUeD/upload_OQYmDx-icon.png">
                        </div>
                        <div class="col-lg-10">
                            <div class="row">
                                <div class="col-lg-12"><strong>Batch 19</strong></div>
                                <div class="col-lg-12 limited-description">Batch 19 is derived from a recipe found in an old logbook discovered in brewery archives dating back before 1919, when Prohibition banned beer throughout the country. Prohibition was enforced in Colorado in 1916. The pre-Prohibition style lager delivers a bold, hoppy flavor that is surprisingly well balanced. Test market in Chicago, Milwaukee, San Francisco, San Jose, and Washington DC.</div>
                            </div>
                        </div>
                    </div>
                    <div class="row search-result-row">
                        <div class="col-lg-1">
                            <img src="https://s3.amazonaws.com/brewerydbapi/beer/C6EUeD/upload_OQYmDx-icon.png">
                        </div>
                        <div class="col-lg-10">
                            <div class="row">
                                <div class="col-lg-12"><strong>Batch 19</strong></div>
                                <div class="col-lg-12 limited-description">Batch 19 is derived from a recipe found in an old logbook discovered in brewery archives dating back before 1919, when Prohibition banned beer throughout the country. Prohibition was enforced in Colorado in 1916. The pre-Prohibition style lager delivers a bold, hoppy flavor that is surprisingly well balanced. Test market in Chicago, Milwaukee, San Francisco, San Jose, and Washington DC.</div>
                            </div>
                        </div>
                    </div>
                    <div class="row search-result-row">
                        <div class="col-lg-1">
                            <img src="https://s3.amazonaws.com/brewerydbapi/beer/C6EUeD/upload_OQYmDx-icon.png">
                        </div>
                        <div class="col-lg-10">
                            <div class="row">
                                <div class="col-lg-12"><strong>Batch 19</strong></div>
                                <div class="col-lg-12 limited-description">Batch 19 is derived from a recipe found in an old logbook discovered in brewery archives dating back before 1919, when Prohibition banned beer throughout the country. Prohibition was enforced in Colorado in 1916. The pre-Prohibition style lager delivers a bold, hoppy flavor that is surprisingly well balanced. Test market in Chicago, Milwaukee, San Francisco, San Jose, and Washington DC.</div>
                            </div>
                        </div>
                    </div>
                    <div class="row search-result-row">
                        <div class="col-lg-1">
                            <img src="https://s3.amazonaws.com/brewerydbapi/beer/C6EUeD/upload_OQYmDx-icon.png">
                        </div>
                        <div class="col-lg-10">
                            <div class="row">
                                <div class="col-lg-12"><strong>Batch 19</strong></div>
                                <div class="col-lg-12 limited-description">Batch 19 is derived from a recipe found in an old logbook discovered in brewery archives dating back before 1919, when Prohibition banned beer throughout the country. Prohibition was enforced in Colorado in 1916. The pre-Prohibition style lager delivers a bold, hoppy flavor that is surprisingly well balanced. Test market in Chicago, Milwaukee, San Francisco, San Jose, and Washington DC.</div>
                            </div>
                        </div>
                    </div>
                    <div class="row search-result-row">
                        <div class="col-lg-1">
                            <img src="https://s3.amazonaws.com/brewerydbapi/beer/C6EUeD/upload_OQYmDx-icon.png">
                        </div>
                        <div class="col-lg-10">
                            <div class="row">
                                <div class="col-lg-12"><strong>Batch 19</strong></div>
                                <div class="col-lg-12 limited-description">Batch 19 is derived from a recipe found in an old logbook discovered in brewery archives dating back before 1919, when Prohibition banned beer throughout the country. Prohibition was enforced in Colorado in 1916. The pre-Prohibition style lager delivers a bold, hoppy flavor that is surprisingly well balanced. Test market in Chicago, Milwaukee, San Francisco, San Jose, and Washington DC.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Bootstrap core JavaScript
        ================================================== -->
        <!-- Placed at the end of the document so the pages load faster -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="../../assets/js/vendor/jquery.min.js"><\/script>')</script>
        <script src="../../dist/js/bootstrap.min.js"></script>
        <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
        <script src="../../assets/js/ie10-viewport-bug-workaround.js"></script>
    </body>
</html>
