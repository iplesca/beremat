<div class="row">
    <div class="col-lg-1">
        <img class="" src="{{ $beer['image']['medium'] }}">
    </div>
    <div class="col-lg-offset-2 col-lg-6">
        <div class="row">
            <div class="col-lg-12"><h3>{{ $beer['name'] }}</h3></div>
            @if ($beer['description'])
                <div class="col-lg-12">{{ $beer['description'] }}</div>
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