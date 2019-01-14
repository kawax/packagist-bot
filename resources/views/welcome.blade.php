<!doctype html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Packagist @ CloudFront</title>

    @include('analytics')

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css"
          integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
</head>
<body>
<div class="container">

    <div class="jumbotron">
        <h1>
            Yet Another Packagist Mirror
        </h1>
        <div>
            <a href="https://packagist.org/mirrors" target="_blank" class="btn btn-outline-secondary">Packagist
                Mirrors</a>
            <a href="https://github.com/kawax/packagist-bot" target="_blank"
               class="btn btn-outline-secondary">GitHub</a>
            <a href="{{ config('services.discord.url') }}" target="_blank" class="btn btn-outline-secondary">Discord</a>
        </div>
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


<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
        crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js"
        integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut"
        crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js"
        integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k"
        crossorigin="anonymous"></script>

</body>
</html>
