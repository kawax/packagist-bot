<section class="hero is-dark">
    <div class="hero-head">
        <nav class="navbar">
            <div class="container">
                <div class="navbar-end">
                    <div class="navbar-brand">
                        <a href="https://packagist.org/mirrors" target="_blank" rel="noopener noreferrer" class="navbar-item">
                            Packagist Mirrors
                        </a>
                        <a href="https://github.com/kawax/packagist-bot" target="_blank" rel="noopener noreferrer" class="navbar-item">
                            GitHub
                        </a>
                        <a href="{{ config('services.discord.url') }}" target="_blank" rel="noopener noreferrer" class="navbar-item">
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
                        <span class="tag is-white">{{ cache('info_size', 0) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
