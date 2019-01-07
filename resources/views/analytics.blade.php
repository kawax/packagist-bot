@unless(empty(config('packagist.analytics')))
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ config('packagist.analytics') }}"></script>
    <script>
        window.dataLayer = window.dataLayer || []

        function gtag () {dataLayer.push(arguments)}

        gtag('js', new Date())

        gtag('config', '{{ config('packagist.analytics') }}')
    </script>
@endunless
