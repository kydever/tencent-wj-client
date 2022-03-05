# 腾讯问卷客户端

[![PHPUnit](https://github.com/kydever/tencent-wj-client/actions/workflows/test.yml/badge.svg)](https://github.com/kydever/tencent-wj-client/actions/workflows/test.yml)

```
composer require kydev/tencent-wj-client
```

## 使用

具体使用方法请查看 [腾讯问卷文档](https://wj.qq.com/docs/openapi)

### 快速开始

`Hyperf` 框架中，可以直接使用 `KY\Tencent\WJClient`。

1. 发布配置

```shell
php bin/hyperf.php vendor:publish kydev/tencent-wj-client
```

2. 注入并使用

```php
<?php

use KY\Tencent\WJClient\Factory;
use Hyperf\Di\Annotation\Inject;

class IndexController
{
    #[Inject]
    public Factory $factory;
    
    public function index()
    {
        return $this->factory->get('default')->user->info('1');
    }
}
```

其他框架，可以自行 `new Application()` 使用。

```php
<?php

use KY\Tencent\WJClient\Application;

$app = new Application([
    'app_id' => 'xxx',
    'app_secret' => 'xxx',
    'http' => [
        'base_uri' => 'https://open.wj.qq.com/',
        'http_errors' => false,
        'timeout' => 2,
    ],
]);

$result = $app->user->info('1');
```

