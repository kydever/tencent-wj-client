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
class CodeTest extends AbstractTestCase
{
    public function testGetCode()
    {
        $app = $this->getAppWithCache();

        $info = $app->user->info('1');

        $result = $app->code->getCode($info['user_id'], 'user');

        $this->assertIsString($result);

        // $result = $app->code->getCode($info['respondent_id']);
        //
        // $this->assertIsString($result);
    }
}
