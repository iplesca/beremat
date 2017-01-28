@if (Session::get('currentBeer'))
<div class="row">
    <div class="col-lg-1">
        <img class="" src="{{ Session::get('currentBeer')['image_medium'] }}">
    </div>
    <div class="col-lg-offset-2 col-lg-6">
        <div class="row">
            <div class="col-lg-12"><h3>{{ Session::get('currentBeer')['name'] }}</h3></div>
            
            @include('abv')            
            @if (Session::get('currentBeer')['description'])
                <div class="col-lg-12">{{ Session::get('currentBeer')['description'] }}</div>
            @else
                <div class="col-lg-12"><em>No description provided.</em></div>
            @endif
        </div>
    </div>
    <div class="col-lg-2">
        <a href="{{ route('randomBeer') }}" class="btn btn-primary btn-lg">Another beer</a>
        <a href="{{ route('sameBrewery') }}" class="btn btn-primary btn-lg">More from this brewery</a>
    </div>
</div>
@endif