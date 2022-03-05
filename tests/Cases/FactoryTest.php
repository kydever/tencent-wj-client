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
namespace HyperfTest\Cases;

use KY\Tencent\WJClient\Cache\NullCache;
use KY\Tencent\WJClient\Factory;
use Psr\SimpleCache\CacheInterface;

/**
 * @internal
 * @coversNothing
 */
class FactoryTest extends AbstractTestCase
{
    public function testFactoryMake()
    {
        $container = $this->getContainer();
        $container->shouldReceive('has')->with(CacheInterface::class)->andReturnFalse();
        $app = $container->get(Factory::class)->make('default');

        $this->assertInstanceOf(NullCache::class, $app->cache);
    }
}
