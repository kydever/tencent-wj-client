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

use GuzzleHttp;
use GuzzleHttp\RequestOptions;
use Hyperf\Utils\Codec\Json;
use KY\Tencent\WJClient\AccessTokenInterface;
use KY\Tencent\WJClient\Exception\RuntimeException;
use KY\Tencent\WJClient\Exception\TokenInvalidException;
use KY\Tencent\WJClient\ProviderInterface;
use Pimple\Container;
use Psr\Http\Message\ResponseInterface;

class Client implements ProviderInterface
{
    public function __construct(protected Container $pimple, protected array $config)
    {
    }

    public function client(?AccessTokenInterface $token = null): GuzzleHttp\Client
    {
        $config = $this->config;
        if ($token) {
            $config[RequestOptions::QUERY]['appid'] = $token->getAppId();
            $config[RequestOptions::QUERY]['access_token'] = $token->getToken();
        }

        return make(GuzzleHttp\Client::class, [$config]);
    }

    public function handleResponse(ResponseInterface $response): array
    {
        $ret = Json::decode((string) $response->getBody());
        if ($ret['code'] !== 'OK') {
            $code = (int) $ret['code'];
            if ($code >= 99991661 && $code <= 99991668) {
                throw new TokenInvalidException();
            }

            throw new RuntimeException($ret['error']['type'] ?? 'http request failed.');
        }

        return $ret;
    }

    public static function getName(): string
    {
        return 'http';
    }
}
