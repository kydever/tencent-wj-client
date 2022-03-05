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

use KY\Tencent\WJClient\AccessToken\AccessToken;
use KY\Tencent\WJClient\Exception\RuntimeException;
use KY\Tencent\WJClient\Factory;
use Psr\SimpleCache\CacheInterface;

/**
 * @internal
 * @coversNothing
 */
class UserTest extends AbstractTestCase
{
    public function testUserRegister()
    {
        $container = $this->getContainer();
        $container->shouldReceive('has')->with(CacheInterface::class)->andReturnTrue();
        $container->shouldReceive('get')->with(CacheInterface::class)->andReturn($cache = \Mockery::mock(CacheInterface::class));
        $cache->shouldReceive('get')->with(AccessToken::CACHE_KEY)->once()->andReturnNull();
        $cache->shouldReceive('set')->with(AccessToken::CACHE_KEY, \Mockery::any(), 7200)->once()->andReturnTrue();
        $app = $container->get(Factory::class)->make('default');

        try {
            $result = $app->user->register('1', '李铭昕', 'https://en.gravatar.com/userimage/117090979/5a6aa84ba4e7893285cbb5578a033a9a.jpg?size=200');

            $this->assertIsArray($result);
            $this->assertArrayHasKey('user_id', $result);
            $this->assertArrayHasKey('respondent_id', $result);
        } catch (RuntimeException $exception) {
            $this->assertSame('openid_existed', $exception->getMessage());
        }
    }
}
