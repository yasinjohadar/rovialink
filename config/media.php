<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Media filesystem disk mapping name
    |--------------------------------------------------------------------------
    |
    | Maps to StorageDiskMapping.disk_name (default: public).
    | Product, category, and hero images use this disk with cloud-first
    | upload and local fallback.
    |
    */

    'disk' => env('MEDIA_DISK', 'public'),

    /*
    | All StorageDiskMapping names used for product/category/hero media.
    | Storages are merged and cloud drivers are tried before local.
    | Must include MEDIA_DISK; add "images" if S3 is mapped there in admin.
    */
    'disks' => array_values(array_filter(array_map(
        'trim',
        explode(',', env('MEDIA_DISKS', 'public,images'))
    ))),

    /*
    | Signed URLs for S3-compatible storage (iDrive, private buckets).
    | Avoids broken links when cdn_url points at the API endpoint (HTML response).
    */
    's3_use_signed_urls' => env('MEDIA_S3_SIGNED_URLS', true),
    'signed_url_ttl_minutes' => (int) env('MEDIA_SIGNED_URL_TTL', 10080),

];
