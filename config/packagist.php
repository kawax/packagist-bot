<?php
return [
    /**
     * GuzzleClientのオプション
     */
    'guzzle'      => [
        'base_uri' => 'https://packagist.org',
    ],

    /**
     * storage内のpath
     */
    'path'        => 'packagist/public/',

    /**
     * 同時接続数
     */
    'concurrency' => 10,
];
