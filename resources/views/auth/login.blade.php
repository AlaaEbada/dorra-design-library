@extends('layouts.base')
@section('content')
<h1>Dorra — Design Library</h1>
<p>Sign in with your internal account.</p>
<form method="POST" action="{{ route('login') }}">
    @csrf
    <label for="email">Email</label>
    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
    @error('email')<p class="error" role="alert">{{ $message }}</p>@enderror
    <label for="password">Password</label>
    <input id="password" type="password" name="password" required autocomplete="current-password">
    @error('password')<p class="error" role="alert">{{ $message }}</p>@enderror
    <label><input type="checkbox" name="remember" value="1"> Remember me</label>
    <button type="submit">Sign in</button>
</form>
@endsection
