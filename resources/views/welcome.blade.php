<!doctype html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Packagist</title>

    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ config('analytics.tracking') }}"></script>
    <script>
        window.dataLayer = window.dataLayer || []

        function gtag () {dataLayer.push(arguments)}

        gtag('js', new Date())

        gtag('config', '{{ config('analytics.tracking') }}')
    </script>

</head>
<body>
<div class="uk-flex uk-flex-center uk-height-1-1">

    <div class="uk-container-xsmall uk-position-center uk-text-center">

        <h1 class="uk-heading-primary">
            Packagist
        </h1>

        <div class="uk-heading-hero uk-text-primary">
            {{ $last_updated }}
        </div>

        <div class="uk-margin">
            <a href="https://discord.gg/req6FYE" class="uk-button uk-button-secondary uk-border-rounded">Discord</a>
        </div>
    </div>
</div>
</body>
</html>
