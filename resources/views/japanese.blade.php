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
            S3+CloudFrontで完全に静的に配信してるので動的な機能はDiscord botを経由して行う。#packagistチャンネルでのみ有効。
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
            <li>1時間に1回の更新。50分開始。CloudFrontのキャッシュは残る。急ぐならDiscordで操作する想定。</li>
            <li>このページ自体キャッシュされてるのでLast-Modifiedは参考にならない。Discord内の通知が正確。ここは普段見ないので優先度は低い。</li>
            <li>12時にキャッシュ強制削除。12時過ぎから自動composer updateするため。</li>
            <li>現状は自分で使うためだけの設定。ユーザーが増えたら調整。</li>
            <li>最短5分間隔、毎回キャッシュ削除までは可能。</li>
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
            <li>
                <a href="https://www.pixiv.net/fanbox/creator/762638/post/268311"
                   target="_blank"
                   rel="noopener noreferrer">packagist-botの動かし方</a>
            </li>
        </ul>
    </div>
</article>
