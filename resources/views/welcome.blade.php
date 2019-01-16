<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('analytics')

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Packagist @ CloudFront</title>

    <meta name="twitter:card" content="summary">
    <meta name="twitter:site" content="@kawaxbiz">
    <meta name="twitter:title" content="Yet Another Packagist Mirror">
    <meta name="twitter:description" content="Yet Another Packagist Mirror">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.7.2/css/bulma.min.css">
</head>
<body>

<section class="hero is-dark">
    <div class="hero-head">
        <nav class="navbar">
            <div class="container">
                <div class="navbar-end">
                    <div class="navbar-brand">
                        <a href="https://packagist.org/mirrors" target="_blank" class="navbar-item">
                            Packagist Mirrors
                        </a>
                        <a href="https://github.com/kawax/packagist-bot" target="_blank" class="navbar-item">
                            GitHub
                        </a>
                        <a href="{{ config('services.discord.url') }}" target="_blank" class="navbar-item">
                            Discord
                        </a>
                    </div>
                </div>
            </div>
        </nav>
    </div>

    <div class="hero-body">
        <div class="container">
            <h1 class="title is-1">
                Yet Another Packagist Mirror
            </h1>

            <article class="message is-info">
                <div class="message-header">
                    <p>Enable</p>
                </div>
                <div class="message-body">
                    composer config -g repos.packagist composer https://packagist.kawax.biz/
                </div>
            </article>

            <article class="message is-info">
                <div class="message-header">
                    <p>Disable</p>
                </div>
                <div class="message-body">
                    composer config -g --unset repos.packagist
                </div>
            </article>

            <div class="field is-grouped is-grouped-multiline">
                <div class="control">
                    <div class="tags has-addons">
                        <span class="tag is-info">count</span>
                        <span class="tag is-white">{{ cache('info_count', 0) }}</span>
                    </div>
                </div>

                <div class="control">
                    <div class="tags has-addons">
                        <span class="tag is-info">size</span>
                        <span class="tag is-white"> {{ cache('info_size', 0) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<div class="section has-background-whire">
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

        <article class="message is-info">
            <div class="message-header">
                <p>Hosting</p>
            </div>
            <div class="message-body">
                AWS S3(Tokyo) + CloudFront(All Edge Locations)
            </div>
        </article>

        <article class="message is-danger">
            <div class="message-body">
                Don't mirror from this server. Use original packagist.org.
            </div>
        </article>
    </div>
</div>

</body>
</html>
