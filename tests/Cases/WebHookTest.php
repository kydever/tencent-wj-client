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

/**
 * @internal
 * @coversNothing
 */
class WebHookTest extends AbstractTestCase
{
    public function testWebHooks()
    {
        $app = $this->getAppWithCache();

        $res = $app->webhook->create(9798596, 'https://api.github.com/', false);

        $this->assertArrayHasKey('id', $res);
        $this->assertArrayHasKey('url', $res);
        $this->assertArrayHasKey('is_active', $res);

        $res = $app->webhook->get(9798596);

        $this->assertArrayHasKey('list', $res);
        $this->assertArrayHasKey('total', $res);

        foreach ($res['list'] as $item) {
            if ($item['url'] === 'https://api.github.com/') {
                $info = $app->webhook->first(9798596, $item['id']);
                $this->assertArrayHasKey('id', $info);
                $this->assertArrayHasKey('url', $info);
                $this->assertArrayHasKey('is_active', $info);
                $this->assertTrue($app->webhook->delete(9798596, $item['id']));
            }
        }
    }
}
