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
namespace KY\Tencent\WJClient\Cache;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Psr\Container\ContainerInterface;
use Psr\SimpleCache\CacheInterface;

class SimpleCacheProvider implements ServiceProviderInterface
{
    public function __construct(private ContainerInterface $container)
    {
    }

    public function register(Container $pimple)
    {
        if (! $this->container->has(CacheInterface::class)) {
            $pimple['cache'] = new NullCache();
            return;
        }

        $pimple['cache'] = $this->container->get(CacheInterface::class);
    }
}
