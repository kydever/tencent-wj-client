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
namespace KY\Tencent\WJClient\Config;

use JetBrains\PhpStorm\ArrayShape;
use KY\Tencent\WJClient\ProviderInterface;

class Config implements ProviderInterface
{
    /**
     * @param $config = [
     *     'app_id' => '',
     *     'app_secret' => '',
     *     'http' => [
     *         'base_uri' => 'https://open.wj.qq.com/',
     *         'timeout' => 2,
     *         'http_errors' => false,
     *     ],
     * ]
     */
    public function __construct(protected array $config)
    {
    }

    public function getAppId(): string
    {
        return $this->config['app_id'];
    }

    public function getAppSecret(): string
    {
        return $this->config['app_secret'];
    }

    #[ArrayShape(['base_uri' => 'string', 'timeout' => 'int'])]
    public function getHttp(): array
    {
        return $this->config['http'];
    }

    public function toArray(): array
    {
        return $this->config;
    }

    public static function getName(): string
    {
        return 'config';
    }
}
