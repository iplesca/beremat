@extends('layout')

@section('title', 'Bere-0-mat :: ' . Session::get('currentBeer')['name'])

@section('current_beer')
@include('current_beer')
@endsection

@section('search_results')
    @if (isset($searchResults))
        @include('search_results', ['collection' => $searchResults])
    @endif
@endsection