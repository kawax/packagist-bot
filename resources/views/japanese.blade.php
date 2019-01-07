<div class="tab-pane fade show active" id="ja" role="tabpanel" aria-labelledby="ja-tab">


    <div class="lead my-3">
        <a href="https://packagist.jp/" target="_blank">packagist.jp</a>と同じようなミラーサイト。
    </div>


    <div class="alert alert-primary" role="alert">
        Last-Modified : {{ $last->tz('Asia/Tokyo')->toIso8601String() }} JST
    </div>

    <h2>Discordコマンド</h2>

    <div>

    </div>


    <a href="https://discord.gg/req6FYE" class="btn btn-outline-secondary">Discord</a>

    <dl class="row">
        <dt class="col-1">Reload</dt>
        <dd class="col-11">
            <p><code>@packagist /reload</code> メタファイルを更新</p>
            <p><code>@packagist /r</code> 短縮形</p>
        </dd>

        <dt class="col-1">Purge</dt>
        <dd class="col-11">
            <p><code>@packagist /purge</code> CloudFrontキャッシュを削除</p>
            <p><code>@packagist /p</code> 短縮形</p>
        </dd>
    </dl>

    <h2>連絡先</h2>
    <div>
        更新が止まってる場合はDiscordかTwitterかQiitadon辺りから連絡してもらえれば。
    </div>
</div>
