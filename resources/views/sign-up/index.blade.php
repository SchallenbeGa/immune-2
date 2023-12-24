@extends('layouts.welcome', [
  'navbar_active' => $navbar_active ?? null
])

@section('content')
  @include('sign-up.partials.index')
@endsection