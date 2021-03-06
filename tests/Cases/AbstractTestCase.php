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
namespace HyperfTest\Cases;

use Dotenv\Dotenv;
use Dotenv\Repository\Adapter;
use Dotenv\Repository\RepositoryBuilder;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Hyperf\Config\Config;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Di\Container;
use Hyperf\Utils\ApplicationContext;
use Hyperf\Utils\Codec\Json;
use KY\Tencent\WJClient\Cache\SimpleCacheProvider;
use KY\Tencent\WJClient\Factory;
use Mockery;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\SimpleCache\CacheInterface;

/**
 * Class AbstractTestCase.
 */
abstract class AbstractTestCase extends TestCase
{
    protected bool $isMock = true;

    protected ?string $token = null;

    protected function tearDown(): void
    {
        Mockery::close();
    }

    protected function getContainer(): Mockery\MockInterface|ContainerInterface
    {
        $container = Mockery::mock(Container::class);
        ApplicationContext::setContainer($container);

        $container->shouldReceive('make')->with(Client::class, Mockery::any())->andReturnUsing(function ($_, $args) {
            if ($this->isMock) {
                $client = Mockery::mock(Client::class);
                $client->shouldReceive('request')->withAnyArgs()->andReturnUsing(function ($method, $uri, $args) {
                    return new Response(200, [], $this->getContent($uri, $method));
                });
                return $client;
            }
            return new Client(...$args);
        });

        $container->shouldReceive('get')->with(Factory::class)->andReturn(new Factory($this->getConfig()));
        $container->shouldReceive('make')->with(SimpleCacheProvider::class, Mockery::any())->andReturn(new SimpleCacheProvider($container));

        return $container;
    }

    protected function getFactory()
    {
        $this->getContainer();

        return new Factory($this->getConfig());
    }

    protected function getContent(string $uri, string $method): string
    {
        $path = BASE_PATH . '/tests/json/';
        $maps = [
            'api/oauth2/access_token' => value(function () use ($path) {
                $result = Json::decode(file_get_contents($path . 'access_token.json'));
                $result['data']['access_token'] = uniqid();
                return Json::encode($result);
            }),
            'api/sso/users' => file_get_contents($path . 'user_register.json'),
            'api/sso/users/1' => file_get_contents($path . 'user_info.json'),
            'api/sso/users/2' => file_get_contents($path . 'invalid_token.json'),
            'api/sso/code' => file_get_contents($path . 'code.json'),
            'api/surveys/9798596/respondent/access_list/batch' => file_get_contents($path . 'batch_add_access_list.json'),
            'api/surveys/9798596/webhooks' => static function () use ($method, $path) {
                return match ($method) {
                    'POST' => file_get_contents($path . 'create_hook.json'),
                    'GET' => file_get_contents($path . 'get_hook.json'),
                };
            },
            'api/surveys/9798596/webhooks/10013204' => static function () use ($method, $path) {
                return match ($method) {
                    'DELETE' => file_get_contents($path . 'delete_hook.json'),
                    'GET' => file_get_contents($path . 'first_hook.json'),
                };
            },
        ];

        $body = $maps[$uri];
        if ($body instanceof \Closure) {
            return $body();
        }
        return $body;
    }

    protected function getConfig(): ConfigInterface
    {
        if (file_exists(BASE_PATH . '/.env')) {
            $this->isMock = false;
            $this->loadDotenv();
        }

        return new Config([
            'tencent_wj' => [
                'http' => [
                    'base_uri' => 'https://open.wj.qq.com/',
                    'http_errors' => false,
                    'timeout' => 2,
                ],
                'applications' => [
                    'default' => [
                        'app_id' => env('TENCENT_WJ_APPID', 'xxx'),
                        'app_secret' => env('TENCENT_WJ_SECRET', ''),
                        'providers' => [
                            \KY\Tencent\WJClient\Cache\SimpleCacheProvider::class,
                        ],
                    ],
                ],
            ],
        ]);
    }

    protected function loadDotenv(): void
    {
        $repository = RepositoryBuilder::createWithNoAdapters()
            ->addAdapter(Adapter\PutenvAdapter::class)
            ->immutable()
            ->make();

        Dotenv::create($repository, [BASE_PATH])->load();
    }

    protected function getAppWithoutCache()
    {
        $container = $this->getContainer();
        $container->shouldReceive('has')->with(CacheInterface::class)->andReturnFalse();
        return $container->get(Factory::class)->make('default');
    }

    protected function getAppWithCache()
    {
        $container = $this->getContainer();
        $container->shouldReceive('has')->with(CacheInterface::class)->andReturnTrue();
        $container->shouldReceive('get')->with(CacheInterface::class)->andReturn($cache = \Mockery::mock(CacheInterface::class));
        $cache->shouldReceive('get')->with(\Mockery::any())->andReturnUsing(function () {
            return $this->token;
        });
        $cache->shouldReceive('set')->with(\Mockery::any(), \Mockery::any(), 7200)->andReturnUsing(function ($key, $token) {
            $this->token = $token;
            return true;
        });
        return $container->get(Factory::class)->make('default');
    }
}
