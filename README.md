# Btree

[![Latest Version](https://img.shields.io/github/release/assassin215k/btree.svg?style=flat-square)](https://github.com/assassin215k/btree/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Coverage Status](https://img.shields.io/coveralls/github/assassin215k/btree/master?style=flat-square)](https://coveralls.io/github/assassin215k/btree?branch=master)
[![Coverage Status](https://img.shields.io/coveralls/github/assassin215k/btree/dev?color=lightgray&label=dev%20coverage)](https://coveralls.io/github/assassin215k/btree?branch=dev)
[![Quality Score](https://img.shields.io/scrutinizer/g/assassin215k/btree.svg?style=flat-square)](https://scrutinizer-ci.com/g/assassin215k/btree)
[![Total Downloads](https://img.shields.io/packagist/dt/assassin215k/btree.svg?style=flat-square)](https://packagist.org/packages/assassin215k/btree)

Provides btree-indexation for an object collection. Provide sorting, ordering and composite indexes.
Writes with PSR12 support

## Install

Via Composer

``` bash
$ composer require assassin215k/btree
```

## Usage

``` php
$entryPoint = new Assassin215k\Btree();
echo $entryPoint->echoPhrase('Hello, World!');
```

## Testing

``` bash
$ phpunit
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email info@iceorb.com.ua instead of using the issue tracker.

## Credits

- [Ihor Fedan](https://github.com/assassin215k)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
