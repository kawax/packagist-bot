<div class="tab-pane fade show active" id="ja" role="tabpanel" aria-labelledby="ja-tab">

    <p class="lead my-3">
        <a href="https://packagist.jp/" target="_blank">packagist.jp</a>と同じようなミラーサイト。同じなので特に説明はない。
    </p>

    <div class="alert alert-secondary" role="alert">
        Last-Modified : {{ $last->tz('Asia/Tokyo')->toIso8601String() }} JST
    </div>

    <h3 class="my-3">Discordコマンド</h3>

    <p>
        S3+CloudFrontで完全に静的に配信してるので動的な機能はDiscord botを経由して行う。#packagistチャンネルでのみ有効。
    </p>

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
    <ul>
        <li>1時間に1回の更新。50分開始。CloudFrontのキャッシュは残る。急ぐならDiscordで操作する想定。</li>
        <li>このページ自体キャッシュされてるのでLast-Modifiedは参考にならない。Discord内の通知が正確。ここは普段見ないので優先度は低い。</li>
        <li>12時にキャッシュ強制削除。12時過ぎから<a href="https://cu.kawax.biz/" target="_blank">自動composer update</a>するため。</li>
        <li>現状は自分で使うためだけの設定。ユーザーが増えたら調整。</li>
    </ul>


    <h3>連絡先</h3>
    <ul>
        <li>更新が止まってたら
            <a href="{{ config('services.discord.url') }}" target="_blank">Discord</a>
            か
            <a href="https://twitter.com/kawaxbiz" target="_blank">Twitter</a>
            か
            <a href="https://qiitadon.com/@kawax" target="_blank">Qiitadon</a>
            辺りから連絡してもらえれば。
        </li>
    </ul>

    <h3>終了条件</h3>
    <ul>
        <li>運用費用が高くなりすぎたら終了。スポンサーか代わりに運用できる会社を募集する。</li>
        <li>フロントは静的だけどバックエンド側はLaravel系技術を遠慮なく使ってるので意外と難しいかも。</li>
        <li>2019年1月時点でのデータ量はファイル数20万以上、容量は3GBほど。</li>
    </ul>
</div>
