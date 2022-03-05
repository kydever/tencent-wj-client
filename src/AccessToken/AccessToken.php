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

use KY\Tencent\WJClient\AccessTokenInterface;
use KY\Tencent\WJClient\Config\Config;
use KY\Tencent\WJClient\Http\Client;
use KY\Tencent\WJClient\ProviderInterface;

class AccessToken implements AccessTokenInterface, ProviderInterface
{
    public function __construct(protected Config $config, protected Client $client)
    {
    }

    public static function getName(): string
    {
        return 'access_token';
    }

    public function getToken(bool $refresh = false): string
    {
    }
}
