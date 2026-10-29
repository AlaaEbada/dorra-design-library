<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dorra — Design Library</title>
    <link rel="stylesheet" href="{{ asset('css/library.css') }}">
</head>
<body>
    <a class="skip-link" href="#main">Skip to content</a>
    <main id="main" class="@yield('page-class', 'auth-shell')">@yield('content')</main>
</body>
</html>
