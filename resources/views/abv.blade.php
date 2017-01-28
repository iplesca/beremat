@php
$abv = !empty(Session::get('currentBeer')['abv']) ? Session::get('currentBeer')['abv'] : 0;
$abv_min = !empty(Session::get('currentBeer')['abv_min']) ? Session::get('currentBeer')['abv_min'] : 0;
$abv_max = !empty(Session::get('currentBeer')['abv_max']) ? Session::get('currentBeer')['abv_max'] : 0;
@endphp
@if (!empty($abv))
<div class="col-lg-12">
<strong>ABV:</strong> {{ $abv }}%
    @if (!empty($abv_min) || !empty($abv_min))
        &nbsp;[
        @if (!empty($abv_min))
            min: {{ $abv_min }}%
        @endif
        @if (!empty($abv_max))
            max: {{ $abv_max }}%
        @endif
        ]
    @endif
</div>
@endif