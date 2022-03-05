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

use GuzzleHttp\RequestOptions;
use Hyperf\Utils\Codec\Json;
use KY\Tencent\WJClient\AccessTokenInterface;
use KY\Tencent\WJClient\Config\Config;
use KY\Tencent\WJClient\Exception\RequestException;
use KY\Tencent\WJClient\Http\Client;
use KY\Tencent\WJClient\ProviderInterface;
use Psr\SimpleCache\CacheInterface;

class AccessToken implements AccessTokenInterface, ProviderInterface
{
    public const CACHE_KEY = 'ky:tencent_wj:access_token';

    public function __construct(protected Config $config, protected Client $client, protected CacheInterface $cache)
    {
    }

    public static function getName(): string
    {
        return 'access_token';
    }

    public function getToken(bool $refresh = false): string
    {
        if (! $refresh && $token = $this->cache->get(self::CACHE_KEY)) {
            return $token;
        }

        $response = $this->client->client()->request(
            'GET',
            'api/oauth2/access_token',
            [
                RequestOptions::QUERY => [
                    'appid' => $this->config->getAppId(),
                    'secret' => $this->config->getAppSecret(),
                    'grant_type' => 'client_credential',
                ],
            ]
        );

        $result = Json::decode((string) $response->getBody());
        if (empty($result['data']['access_token']) || empty($result['data']['expires_in'])) {
            RequestException::throwException();
        }

        $this->cache->set(
            self::CACHE_KEY,
            $result['data']['access_token'],
            max($result['data']['expires_in'], 60)
        );

        return $result['data']['access_token'];
    }

    public function getAppId(): string
    {
        return $this->config->getAppId();
    }
}
