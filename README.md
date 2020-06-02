# Macroable

<!-- BADGES_START -->
[![Latest Version][badge-release]][packagist]
[![Software License][badge-license]][license]
[![PHP Version][badge-php]][php]
![run-tests](https://github.com/JustSteveKing/macroable/workflows/run-tests/badge.svg)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/JustSteveKing/macroable/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/JustSteveKing/macroable/?branch=master)
[![Total Downloads][badge-downloads]][downloads]

[badge-release]: https://img.shields.io/packagist/v/juststeveking/macroable.svg?style=flat-square&label=release
[badge-license]: https://img.shields.io/packagist/l/juststeveking/macroable.svg?style=flat-square
[badge-php]: https://img.shields.io/packagist/php-v/juststeveking/macroable.svg?style=flat-square

[badge-downloads]: https://img.shields.io/packagist/dt/juststeveking/macroable.svg?style=flat-square&colorB=mediumvioletred

[packagist]: https://packagist.org/packages/juststeveking/macroable
[license]: https://github.com/JustSteveKing/macroable/blob/master/LICENSE
[php]: https://php.net
[downloads]: https://packagist.org/packages/juststeveking/macroable
<!-- BADGES_END -->

The purpose of this package is to replicate some functionality from the core of Laravel, but having the ability to use it outside of Laravel itself.

## Installation

To install this package use composer:

```bash
$ composer require juststeveking/macroable
```

You are then free to use this as you need to within your projects.

## Usage

This package is relatively simple to use. The main idea is to extend classes with new methods on the fly, and provide a mixins.

=====

**Basic Usage**

```php
<?php

use JustSteveKing\Support\Macroable;

class ExtendableClass
{
    use Macroable;
    
    protected $client;

    public function __construct()
    {
        $this->client = new Client();
    }
}

$extendable = new ExtendableClass();

$extendable::registerMacro(
    'sendData',
    fn(array $data) => $this->client->post('/data-endpoint', $data)
);


// Call the method directly
$extendable->sendData([
    'data' => [
        'foo' => 'bar'
    ]
]);

// Call the method statically
$extendable::sendData([
    'data' => [
        'foo' => 'bar'
    ]
]);
```

This is the main concept of the package, however you cal also:

```php
<?php

use JustSteveKing\Support\Macroable;

class Invokable
{
    protected $redis;

    public function __construct()
    {
        $this->redis = new Cache();
    }

    public function __invoke(string $key, $data)
    {
        return $this->redis->store($key, $data)
    }
}

class Extendable
{
    use Macroable;
}


$extendable = new Extendable();

$extendable::registerMacro('cache', new Invokable());

$extendable->cache('key', ['foo' => 'bar']);
```

## Tests

```bash
$ phpunit --testdox
```


## Security

If you discover any security related issues, please email juststevemcd@gmail.com instead of using the issue tracker.
