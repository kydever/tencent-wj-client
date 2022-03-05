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
namespace KY\Tencent\WJClient\AccessToken;

use KY\Tencent\WJClient\Config\Config;
use KY\Tencent\WJClient\Http\Client;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class AccessTokenProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple[AccessToken::getName()] = fn () => new AccessToken(
            $pimple[Config::getName()],
            $pimple[Client::getName()],
            $pimple['cache']
        );
    }
}
