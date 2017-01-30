@extends('layout')

@section('title', 'Bere-0-mat :: ' . $beer['name'])

@section('current_beer')
@include('current_beer')
@endsection

@section('search_results')
@include('search_results')
@endsection
