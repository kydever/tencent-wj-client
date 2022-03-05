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

use KY\Tencent\WJClient\Factory;
use Psr\SimpleCache\CacheInterface;

/**
 * @internal
 * @coversNothing
 */
class AccessTokenTest extends AbstractTestCase
{
    public function testGetToken()
    {
        $container = $this->getContainer();
        $container->shouldReceive('has')->with(CacheInterface::class)->andReturnTrue();
        $container->shouldReceive('get')->with(CacheInterface::class)->andReturn($cache = \Mockery::mock(CacheInterface::class));
        $cache->shouldReceive('get')->with(\Mockery::any())->once()->andReturnNull();
        $cache->shouldReceive('set')->with(\Mockery::any(), \Mockery::any(), 7200)->once()->andReturnTrue();
        $app = $container->get(Factory::class)->make('default');

        $token = $app->access_token->getToken();
        $this->assertIsString($token);
    }

    public function testGetTokenFromCache()
    {
        $container = $this->getContainer();
        $container->shouldReceive('has')->with(CacheInterface::class)->andReturnTrue();
        $container->shouldReceive('get')->with(CacheInterface::class)->andReturn($cache = \Mockery::mock(CacheInterface::class));
        $cache->shouldReceive('get')->with(\Mockery::any())->once()->andReturn($uuid = uniqid());
        $app = $container->get(Factory::class)->make('default');

        $token = $app->access_token->getToken();
        $this->assertSame($token, $uuid);
    }
}
