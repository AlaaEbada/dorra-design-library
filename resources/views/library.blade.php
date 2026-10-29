@extends('layouts.base')
@section('content')
<h1>Dorra — Design Library</h1>
<p>Welcome, {{ auth()->user()->name }}.</p>
<p>The design library will be available in a later phase.</p>
@can('create', \App\Policies\LibraryPolicy::class)
    <p>Administrator access enabled.</p>
@endcan
<form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit">Sign out</button>
</form>
@endsection
