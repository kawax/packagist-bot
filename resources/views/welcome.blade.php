<!doctype html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Packagist @ CloudFront</title>

    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ config('analytics.tracking') }}"></script>
    <script>
        window.dataLayer = window.dataLayer || []

        function gtag () {dataLayer.push(arguments)}

        gtag('js', new Date())

        gtag('config', '{{ config('analytics.tracking') }}')
    </script>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"
          integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
            integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
            crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"
            integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49"
            crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"
            integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy"
            crossorigin="anonymous"></script>

</head>
<body>
<div class="container">

    <div class="jumbotron">
        <h1>
            Yet Another Packagist Mirror
        </h1>
    </div>

    <div class="card">
        <div class="card-body">
            <dl>
                <dt>Enable</dt>
                <dd>
                    <code>
                        composer config -g repos.packagist composer https://packagist.kawax.biz/
                    </code>
                </dd>
                <dt>Disable</dt>
                <dd>
                    <code>
                        composer config -g --unset repos.packagist
                    </code>
                </dd>
            </dl>
        </div>
    </div>


    <div class="card mt-3">
        <div class="card-body">
            <h3>Hosting</h3>
            <div>AWS S3(Tokyo) + CloudFront(All Edge Locations)</div>
        </div>
    </div>

    <div class="mt-3">
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="ja-tab" data-toggle="tab" href="#ja" role="tab" aria-controls="ja"
                   aria-selected="true">Japanese</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="en-tab" data-toggle="tab" href="#en" role="tab" aria-controls="en"
                   aria-selected="false">English</a>
            </li>
        </ul>

        <div class="tab-content" id="myTabContent">
            @include('japanese')

            @include('english')
        </div>
    </div>
</div>
</body>
</html>
