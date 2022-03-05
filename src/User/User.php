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
namespace KY\Tencent\WJClient\User;

use GuzzleHttp\RequestOptions;
use JetBrains\PhpStorm\ArrayShape;
use KY\Tencent\WJClient\AccessToken\AccessToken;
use KY\Tencent\WJClient\Exception\RequestException;
use KY\Tencent\WJClient\HasAccessToken;
use KY\Tencent\WJClient\Http\Client;
use KY\Tencent\WJClient\ProviderInterface;

class User implements ProviderInterface
{
    use HasAccessToken;

    public function __construct(protected AccessToken $token, protected Client $client)
    {
    }

    public static function getName(): string
    {
        return 'user';
    }

    /**
     * 注册账户.
     * @see https://wj.qq.com/docs/openapi/sso/create_user
     */
    #[ArrayShape(['user_id' => 'int', 'respondent_id' => 'int'])]
    public function register(string $openid, string $nickname, string $avatar)
    {
        $result = $this->request(
            'POST',
            'api/sso/users',
            [
                RequestOptions::JSON => [
                    'openid' => $openid,
                    'nickname' => $nickname,
                    'avatar' => $avatar,
                ],
            ]
        );

        if (empty($result['data']['user_id']) || empty($result['data']['respondent_id'])) {
            RequestException::throwException();
        }

        return $result['data'];
    }
}
