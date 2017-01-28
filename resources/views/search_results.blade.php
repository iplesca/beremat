<div class="row">
    <h4><strong>Search results</strong></h4>
    @if ($collection)
        @foreach ($collection as $item)
            @if (isset($item['description']))
            <div class="col-lg-12">
                <div class="row search-result-row">
                    <div class="col-lg-1">
                        <img src="{{ $item['image'] }}">
                    </div>
                    <div class="col-lg-11">
                        <div class="row">
                            <div class="col-lg-12"><strong>{{ $item['name'] }}</strong></div>
                            <div class="col-lg-12 limited-description">
                                <div class="text-content">{{ $item['description'] }}</div>
                            </div>
                            <div class="read-more"><span class="prefix">&nbsp;</span><span>&raquo; Read more</span></div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        @endforeach
    @else
    <em>No results found.</em>
    @endif
</div>