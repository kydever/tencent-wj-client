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
namespace KY\Tencent\WJClient;

use Hyperf\Contract\ConfigInterface;
use KY\Tencent\WJClient\Exception\InvalidArgumentException;

class Factory
{
    /**
     * @var Application[]
     */
    protected array $applications = [];

    protected array $config = [];

    public function __construct(ConfigInterface $config)
    {
        $this->config = $this->formatConfig($config);
    }

    public function get(string $name): Application
    {
        $app = $this->applications[$name] ?? null;
        if ($app instanceof Application) {
            return $app;
        }

        return $this->applications[$name] = $this->make($name);
    }

    public function make(string $name): Application
    {
        $config = $this->config['applications'][$name] ?? null;
        if (empty($config)) {
            throw new InvalidArgumentException(sprintf('Config %s is invalid.', $name));
        }

        $http = $this->config['http'] ?? [];
        $config = array_replace_recursive(['http' => $http], $config);

        return tap(new Application($config), static function (Application $app) use ($config) {
            foreach ($config['providers'] ?? [] as $provider) {
                $app->register($provider);
            }
        });
    }

    /**
     * @return [
     *     'http' => [
     *         'base_uri' => '',
     *         'timeout' => 2,
     *     ],
     *     'applications' => [
     *         'default' => [
     *             'app_id' => '',
     *             'app_secret' => '',
     *             'providers' => [],
     *             'http' => [
     *                  'base_uri' => '',
     *                  'timeout' => 2,
     *             ],
     *         ],
     *     ],
     * ]
     */
    private function formatConfig(ConfigInterface $config): array
    {
        return $config->get('tencent_wj', [
            'http' => [
                'base_uri' => 'https://open.wj.qq.com/',
                'http_errors' => false,
                'timeout' => 2,
            ],
            'applications' => [],
        ]);
    }
}
