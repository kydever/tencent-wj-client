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
namespace KY\Tencent\WJClient\WebHook;

use KY\Tencent\WJClient\AccessToken\AccessToken;
use KY\Tencent\WJClient\Http\Client;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class WebHookProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple[WebHook::getName()] = fn () => new WebHook(
            $pimple[AccessToken::getName()],
            $pimple[Client::getName()]
        );
    }
}
