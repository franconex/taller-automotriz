<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Laravel') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="container py-5">
        <h1 class="display-4">{{ config('app.name', 'Laravel') }}</h1>
        <p class="lead">Bienvenido</p>
    </div>
</body>
</html>
