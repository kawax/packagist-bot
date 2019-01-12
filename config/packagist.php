<?php
return [
    /**
     * GuzzleClient options
     */
    'guzzle'      => [
        'base_uri' => 'https://repo.packagist.org/',
    ],

    /**
     * path in storage
     * storage/app/packagist/
     */
    'path'        => 'packagist/',

    /**
     * Max connections
     */
    'concurrency' => env('CONCURRENCY', 5),

    /**
     * S3
     */
    's3'          => [
        //Sync command
        //aws s3 sync . s3://bucket --delete
        'sync' => env('S3_SYNC'),

        'timeout' => (float)env('S3_SYNC_TIMEOUT', 600),
    ],

    /**
     * AWS IAM User
     * S3(read/write), CloudFront(read/write)
     */
    'aws'         => [
        'key'    => env('AWS_KEY'),
        'secret' => env('AWS_SECRET'),
        'region' => env('AWS_REGION', 'ap-northeast-1'),
    ],

    /**
     * CloudFront Distribution ID
     */
    'cloudfront'  => [
        'dist' => env('AWS_CF_DIST'),
    ],

    /**
     * Google Analytics
     */
    'analytics'   => env('GOOGLE_ANALYTICS'),
];
