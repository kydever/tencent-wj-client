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

use GuzzleHttp\RequestOptions;
use JetBrains\PhpStorm\ArrayShape;
use KY\Tencent\WJClient\AccessToken\AccessToken;
use KY\Tencent\WJClient\Exception\RequestException;
use KY\Tencent\WJClient\HasAccessToken;
use KY\Tencent\WJClient\Http\Client;
use KY\Tencent\WJClient\ProviderInterface;

class WebHook implements ProviderInterface
{
    use HasAccessToken;

    public function __construct(protected AccessToken $token, protected Client $client)
    {
    }

    public static function getName(): string
    {
        return 'webhook';
    }

    /**
     * 创建 WebHook.
     * @see https://wj.qq.com/docs/openapi/webhook/create_webhook
     * @param int $id 问卷ID
     */
    #[ArrayShape(['id' => 'int', 'url' => 'string', 'is_active' => 'bool'])]
    public function create(int $id, string $url, bool $isActive = true): array
    {
        $result = $this->request(
            'POST',
            sprintf('api/surveys/%d/webhooks', $id),
            [
                RequestOptions::JSON => [
                    'url' => $url,
                    'is_active' => $isActive,
                ],
            ]
        );

        if (empty($result['data']['id'])) {
            RequestException::throwException();
        }

        return $result['data'];
    }

    /**
     * 问卷 WebHook 列表.
     * @param int $id 问卷ID
     */
    #[ArrayShape(['list' => 'array', 'total' => 'int'])]
    public function get(int $id): array
    {
        $result = $this->request(
            'GET',
            sprintf('api/surveys/%d/webhooks', $id)
        );

        return $result['data'];
    }

    /**
     * 删除 WebHook.
     * @param int $id 问卷ID
     * @param int $webhookId WebHook ID
     */
    public function delete(int $id, int $webhookId): bool
    {
        $result = $this->request(
            'DELETE',
            sprintf('api/surveys/%d/webhooks/%d', $id, $webhookId)
        );

        return $result['data']['result'] === 'success';
    }

    /**
     * WebHook 详情.
     * @param int $id 问卷ID
     * @param int $webhookId WebHook ID
     */
    #[ArrayShape(['list' => 'array', 'total' => 'int'])]
    public function first(int $id, int $webhookId): array
    {
        $result = $this->request(
            'GET',
            sprintf('api/surveys/%d/webhooks/%d', $id, $webhookId)
        );

        return $result['data'];
    }
}
