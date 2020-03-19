<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('analytics')

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Packagist Mirror</title>

    <meta name="twitter:card" content="summary">
    <meta name="twitter:site" content="@kawaxbiz">
    <meta name="twitter:title" content="Yet Another Packagist Mirror">
    <meta name="twitter:description" content="Yet Another Packagist Mirror">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.8.0/css/bulma.min.css">
</head>
<body>

@include('hero')

<div class="section has-background-white">
    <div class="container">

        <div class="columns">
            <div class="column">
                @include('japanese')
            </div>
            <div class="column">
                @include('english')
            </div>
        </div>
    </div>
</div>

<div class="section has-background-dark">
    <div class="container">
        @include('hosting')
    </div>
</div>

</body>
</html>
