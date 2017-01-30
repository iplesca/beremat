<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@yield('title')</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    </head>
    <body>
        <div class="container">
            <h1><a href="{{ route('home') }}">{{ config('app.name') }}</a></h1>
            @yield('current_beer')
            @include('search', ['searchType' => 'beer'])
            @yield('search_results')
        </div>


        <!-- Bootstrap core JavaScript
        ================================================== -->
        <!-- Placed at the end of the document so the pages load faster -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="../../assets/js/vendor/jquery.min.js"><\/script>')</script>
        <script src="../../dist/js/bootstrap.min.js"></script>
        <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
        <script src="../../assets/js/ie10-viewport-bug-workaround.js"></script>
        @if (Session::has('errorMessage'))
            <script>alert("{{ Session::get('errorMessage') }}")</script>
        @endif
        <script>
            $(document).ready(function() {
                $(".search-result-row").each(function() {
                    var s = $(this);
                    var mb = $(this).find('.limited-description');
                    var tb = mb.find('.text-content');
                    if (mb.height() < tb.height()) {
                        $(this).find('.read-more').show().on('click.readmore', function () {
                            mb.removeClass('limited-description');
                            $(this).remove();
                        });
                    }
                });
            });
        </script>
    </body>
</html>
