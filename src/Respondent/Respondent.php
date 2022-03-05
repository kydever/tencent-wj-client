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
namespace KY\Tencent\WJClient\Respondent;

use GuzzleHttp\RequestOptions;
use KY\Tencent\WJClient\AccessToken\AccessToken;
use KY\Tencent\WJClient\HasAccessToken;
use KY\Tencent\WJClient\Http\Client;
use KY\Tencent\WJClient\ProviderInterface;

class Respondent implements ProviderInterface
{
    use HasAccessToken;

    public function __construct(protected AccessToken $token, protected Client $client)
    {
    }

    /**
     * @param int[] $respondentIds
     */
    public function batchAddAccessList(int $surveyId, array $respondentIds, int $type = 11): bool
    {
        $this->request(
            'POST',
            sprintf('api/surveys/%d/respondent/access_list/batch', $surveyId),
            [
                RequestOptions::JSON => [
                    'type' => $type,
                    'respondent_ids' => $respondentIds,
                ],
            ]
        );

        return true;
    }

    public static function getName(): string
    {
        return 'respondent';
    }
}
