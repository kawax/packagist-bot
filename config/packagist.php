<?php
return [
    /**
     * GuzzleClientのオプション
     */
    'guzzle'      => [
        'base_uri' => 'https://packagist.org/',
    ],

    /**
     * storage内のpath
     */
    'path'        => 'packagist/',

    /**
     * 同時接続数
     */
    'concurrency' => 10,

    /**
     * S3 Sync command
     * aws s3 sync . s3://bucket --delete
     */
    's3_sync'     => env('S3_SYNC'),

    'cloudfront' => [
        'key'    => env('AWS_CF_KEY'),
        'secret' => env('AWS_CF_SECRET'),
        'dist'   => env('AWS_CF_DIST'),
    ],

    'analytics' => env('GOOGLE_ANALYTICS'),
];
