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
namespace KY\Tencent\WJClient;

use KY\Tencent\WJClient\AccessToken\AccessTokenProvider;
use KY\Tencent\WJClient\Http\ClientProvider;
use Pimple\Container;
use Psr\SimpleCache\CacheInterface;

/**
 * @property AccessToken\AccessToken $access_token
 * @property Http\Client $http
 * @property CacheInterface $cache
 */
class Application
{
    private Container $container;

    private array $providers = [
        AccessTokenProvider::class,
        ClientProvider::class,
    ];

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
    public function __construct(array $config)
    {
        $config = new Config\Config($config);
        $this->container = new Container([
            'config' => $config,
        ]);

        foreach ($this->providers as $provider) {
            $this->container->register(new $provider());
        }
    }

    public function __get(string $name)
    {
        return $this->container[$name] ?? null;
    }

    public function register(string $provider)
    {
        $this->container->register(make($provider));
    }
}
