<div class="tab-pane fade show active" id="ja" role="tabpanel" aria-labelledby="ja-tab">


    <div class="lead my-3">
        <a href="https://packagist.jp/" target="_blank">packagist.jp</a>と同じようなミラーサイト。
    </div>


    <div class="alert alert-primary" role="alert">
        Last-Modified : {{ $last->tz('Asia/Tokyo')->toIso8601String() }} JST
    </div>

    <h3 class="my-3">Discordコマンド</h3>

    <div>
        S3+CloudFrontで完全に静的に配信してるので動的な機能はDiscord botを経由して行う。#packagistチャンネルでのみ有効。
    </div>

    <div>
        <a href="{{ config('services.discord.url') }}" class="btn btn-outline-secondary">Discord</a>
    </div>


    <dl class="row mt-3">
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

    <h3>設定</h3>
    <div>
        1時間に1回の更新。50分開始。CloudFrontのキャッシュは残る。急ぐならDiscordで操作する想定。
        12時にキャッシュ強制削除。12時過ぎから自動composer updateするため。
        現状は自分で使うためだけの設定。
    </div>

    <h3>連絡先</h3>
    <div>
        更新が止まってる場合はDiscordかTwitterかQiitadon辺りから連絡してもらえれば。
    </div>
</div>
