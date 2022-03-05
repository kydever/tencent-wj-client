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

/**
 * Class AbstractTestCase.
 */
abstract class AbstractTestCase extends TestCase
{
    protected bool $isMock = true;

    protected array $context = [];

    protected function tearDown(): void
    {
        Mockery::close();
        $this->context = [];
    }

    protected function getContainer(): Mockery\MockInterface|ContainerInterface
    {
        $container = Mockery::mock(Container::class);
        ApplicationContext::setContainer($container);

        $container->shouldReceive('make')->with(Client::class, Mockery::any())->andReturnUsing(function ($_, $args) {
            if ($this->isMock) {
                $client = Mockery::mock(Client::class);
                $client->shouldReceive('request')->andReturnUsing(function ($method, $uri, $args) {
                    return new Response(200, [], $this->getContent($uri));
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

    protected function getContent(string $uri): string
    {
        $path = BASE_PATH . '/tests/json/';
        $maps = [
            'api/oauth2/access_token' => value(function () use ($path) {
                $result = Json::decode(file_get_contents($path . 'access_token.json'));
                $result['data']['access_token'] = uniqid();
                return Json::encode($result);
            }),
        ];

        $this->context[$uri] = true;

        return $maps[$uri];
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
                        'app_id' => env('TENCENT_WJ_APPID', ''),
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
}
