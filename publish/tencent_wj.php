<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
return [
    'http' => [
        'base_uri' => 'https://open.wj.qq.com/',
        'http_errors' => false,
        'timeout' => 2,
    ],
    'applications' => [
        'default' => [
            'app_id' => env('TENCENT_WJ_APPID', ''),
            'app_secret' => env('TENCENT_WJ_SECRET', ''),
            'providers' => [
                \KY\Tencent\WJClient\Cache\SimpleCacheProvider::class,
            ],
        ],
    ],
];
