@extends('layout')

@section('title', 'Bere-0-mat :: ' . $beer['name'])

@section('current_beer')
@include('current_beer')
@endsection
