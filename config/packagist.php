<?php
return [
    /**
     * GuzzleClient options
     */
    'guzzle'      => [
        'base_uri' => 'https://packagist.org/',
    ],

    /**
     * path in storage
     * storage/app/packagist/
     */
    'path'        => 'packagist/',

    /**
     * Max connection
     */
    'concurrency' => env('CONCURRENCY', 5),

    /**
     * S3
     */
    's3'          => [
        //Sync command
        //aws s3 sync . s3://bucket --delete
        'sync' => env('S3_SYNC'),

        'bucket' => env('S3_BUCKET'),
    ],

    /**
     * AWS IAM User
     * S3(read/write), CloudFront(read/write), CloudWatch(read)
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

    'analytics' => env('GOOGLE_ANALYTICS'),
];
