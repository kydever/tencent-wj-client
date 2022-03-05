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
namespace KY\Tencent\WJClient\Code;

use GuzzleHttp\RequestOptions;
use KY\Tencent\WJClient\AccessToken\AccessToken;
use KY\Tencent\WJClient\Exception\RequestException;
use KY\Tencent\WJClient\HasAccessToken;
use KY\Tencent\WJClient\Http\Client;
use KY\Tencent\WJClient\ProviderInterface;

class Code implements ProviderInterface
{
    use HasAccessToken;

    public function __construct(protected AccessToken $token, protected Client $client)
    {
    }

    /**
     * @param string $type user:作为问卷编辑者登录; respondent:作为问卷回答者登录
     */
    public function getCode(int $userId, string $type = 'respondent'): string
    {
        $result = $this->request(
            'POST',
            'api/sso/code',
            [
                RequestOptions::JSON => [
                    'user_id' => $userId,
                    'scene_type' => $type,
                ],
            ]
        );

        if (empty($result['data']['code'])) {
            RequestException::throwException();
        }

        return $result['data']['code'];
    }

    public static function getName(): string
    {
        return 'code';
    }
}
