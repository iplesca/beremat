
<div class="row">
    <h4><strong>Search results</strong></h4>
    @if ($collection)
        @foreach ($collection as $item)
            @if (isset($item['description']))
            <div class="col-lg-12">
                <div class="row search-result-row">
                    <div class="col-lg-1">
                        <img src="{{ $item['images']['icon'] }}">
                    </div>
                    <div class="col-lg-10">
                        <div class="row">
                            <div class="col-lg-12"><strong>{{ $item['name'] }}</strong></div>
                            <div class="col-lg-12 limited-description">
                                @if ($item['description'])
                                    {{ $item['description'] }}
                                @else
                                    <em>No desc.</em>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        @endforeach
    @else
    <em>No results found.</em>
    @endif
<!--    <div class="col-lg-12">
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
    </div>-->
</div>