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
namespace KY\Tencent\WJClient\Http;

use KY\Tencent\WJClient\Config\Config;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ClientProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        /** @var Config $config */
        $config = $pimple['config'];
        $pimple[Client::getName()] = fn () => new Client($pimple, $config->getHttp());
    }
}
