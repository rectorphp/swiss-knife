# Easy CI

[![Downloads total](https://img.shields.io/packagist/dt/migrify/easy-ci.svg?style=flat-square)](https://packagist.org/packages/migrify/easy-ci/stats)

Tools that make easy to setup CI.

## Install

```bash
composer require migrify/easy-ci --dev
```

## Usage

```bash
vendor/bin/easy-ci check-conflicts src config
```

### Generate Sonar Cube config file `sonar-project.properties`

This command comes very handy, **if you change, add or remove your paths to your PHP code**. While not very common, it comes handy in monorepo or local packages. No need to update `sonar-project.properties` manually - this command automates it!

First, read [how to enable Sonar Cube for your project](https://tomasvotruba.com/blog/2020/02/24/how-many-days-of-technical-debt-has-your-php-project/).

Then create `easy-ci.php` with following values:

```php
<?php

declare(strict_types=1);

use Migrify\EasyCI\ValueObject\Option;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set(Option::SONAR_ORGANIZATION, 'migrify');
    $parameters->set(Option::SONAR_PROJECT_KEY, 'migrify_migrify');
    // paths to your source, packages and tests
    $parameters->set(Option::SONAR_DIRECTORIES, [
        __DIR__ . '/src',
        __DIR__ . '/tests',
        __DIR__ . '/packages',
    ]);
    
    // optional - for extra parameters
    $parameters->set(Option::SONAR_OTHER_PARAMETERS, [
        'sonar.extra' => 'extra_values',
    ]);
};
```

Last, generate the file:

```bash
vendor/bin/easy-ci generate-sonar
```

That's it!

## Report Issues

In case you are experiencing a bug or want to request a new feature head over to the [migrify monorepo issue tracker](https://github.com/migrify/migrify/issues)

## Contribute

The sources of this package are contained in the migrify monorepo. We welcome contributions for this package on [migrify/migrify](https://github.com/migrify/migrify).
