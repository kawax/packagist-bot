<article class="message is-dark">
    <div class="message-header">
        <p>Japanese</p>
    </div>
    <div class="message-body content">
        <p>
            <a href="https://packagist.jp/" target="_blank" rel="noopener noreferrer">packagist.jp</a>と同じようなミラーサイト。同じなので特に説明はない。
        </p>

        <article class="message">
            <div class="message-body">
                Last-Modified : {{ $last->tz('Asia/Tokyo')->toIso8601String() }} JST
            </div>
        </article>


        <h3>Discordコマンド</h3>

        <p>
            静的に配信してるので動的な機能はDiscord botを経由して行う。#packagistチャンネルでのみ有効。
        </p>

        <dl>
            <dt>Reload</dt>
            <dd>
                <p><code>@packagist /reload</code> メタファイルを更新</p>
                <p><code>@packagist /r</code> 短縮形</p>
            </dd>

            <dt>Purge</dt>
            <dd>
                <p><code>@packagist /purge</code> CloudFrontキャッシュを削除</p>
                <p><code>@packagist /p</code> 短縮形</p>
            </dd>
        </dl>

        <h3>設定</h3>
        <ul>
            <li>1時間に1回の更新。30分開始。急ぐならDiscordで操作する想定。</li>
            <li>1年以上稼働させた結果、困ることはなかったのでしばらくはこの設定。</li>
        </ul>

        <h3>連絡先</h3>
        <ul>
            <li>更新が止まってたら
                <a href="{{ config('services.discord.url') }}"
                   target="_blank"
                   rel="noopener noreferrer">Discord</a>
                か
                <a href="https://twitter.com/kawaxbiz"
                   target="_blank"
                   rel="noopener noreferrer">Twitter</a>
                辺りから連絡してもらえれば。
            </li>
        </ul>

        <h3>終了条件</h3>
        <ul>
            <li>運用費用が高くなりすぎたら終了。スポンサーか代わりに運用できる会社を募集する。(現状はかなり安いので問題なさそう)</li>
            <li>S3への同期が一番時間のかかる重い処理になってたのでS3+CloudFront構成は終了。</li>
        </ul>
    </div>
</article>
