# Swiss Knife for Upgrades

[![Downloads total](https://img.shields.io/packagist/dt/rector/swiss-knife.svg?style=flat-square)](https://packagist.org/packages/rector/swiss-knife/stats)

Swiss knife in the pocket of every upgrade architect!

<br>

## Install

```bash
composer require rector/swiss-knife --dev
```

<br>

---

<br>

## Usage

* [1. Check your Code for Git Merge Conflicts](#1-check-your-code-for-git-merge-conflicts)
* [2. Detect Commented Code](#2-detect-commented-code)
* [3. Reach full PSR-4](#3-reach-full-psr-4)
* [4. Finalize classes without children](#4-finalize-classes-without-children)
* [5. Privatize local class constants](#5-privatize-local-class-constants)
* [6. Spots Fake Traits](#6-spots-fake-traits)
* [7. Split huge Symfony config to per-package in directory](#7-split-huge-symfony-config-to-per-package-in-directory)
* [8. Generate Symfony Smoke Tests](#8-generate-symfony-smoke-tests)
* [9. Detect Duplicated Code](#9-detect-duplicated-code)

<br>

## 1. Check your Code for Git Merge Conflicts

Do you use Git? Then merge conflict markers are the last thing you want to see in pushed code:

```
<<<<<<< HEAD
```

Add this command to CI to spot these:

```bash
vendor/bin/swiss-knife check-conflicts .
```

You can skip paths with the `--exclude` option:

```bash
vendor/bin/swiss-knife check-conflicts . --exclude vendor --exclude tests/fixtures
```

<br>

## 2. Detect Commented Code

Have you ever forgotten commented code in your project?

```php
//      foreach ($matches as $match) {
//           $content = str_replace($match[0], $match[2], $content);
//      }
```

No more! Add this command to CI to spot these:

```bash
vendor/bin/swiss-knife check-commented-code <directory>
vendor/bin/swiss-knife check-commented-code packages --line-limit 5 --skip-file '*Controller.php'
```

<br>

## 3. Reach full PSR-4

### Find multiple classes in single file

To make PSR-4 work properly, each class must be in its own file. This command makes it easy to spot multiple classes in a single file:

```bash
vendor/bin/swiss-knife find-multi-classes src
```

<br>

### Update Namespace to match PSR-4 Root

Is your class in wrong namespace? Make it match your PSR-4 root:

```bash
vendor/bin/swiss-knife namespace-to-psr-4 src --namespace-root "App\\"
```

This will update all files in your `/src` directory, to starts with `App\\` and follow full PSR-4 path:

```diff
 # file path: src/Repository/TalkRepository.php

-namespace Model;
+namespace App\Repository;

 ...
```

<br>

## 4. Finalize classes without children

Do you want to finalize all classes that don't have children?

```bash
vendor/bin/swiss-knife finalize-classes src tests
```

Do you use mocks but not [bypass final](https://tomasvotruba.com/blog/2019/03/28/how-to-mock-final-classes-in-phpunit) yet?

```bash
vendor/bin/swiss-knife finalize-classes src tests --skip-mocked
```

This will keep mocked classes non-final, so PHPUnit can extend them internally.

<br>

Do you want to skip file or two?

```bash
vendor/bin/swiss-knife finalize-classes src tests --skip-file src/SpecialProxy.php
```

Skip is also support with `fnmatch()` patterns:

```bash
vendor/bin/swiss-knife finalize-classes src tests --skip-file '*Controller.php'
```

<br>

## 5. Privatize local class constants

PHPStan can report unused private class constants, but it skips all the public ones.
Do you have lots of class constants, all of them public but want to narrow scope to privates?

```bash
vendor/bin/swiss-knife privatize-constants src test
```

This command will:

* find all class constant usages
* scans classes and constants
* makes those constant used locally `private`

That way all the constants not used outside will be made `private` safely.

<br>

## 6. Split huge Symfony config to per-package in directory

Do you have a huge Symfony config file that is hard to navigate? Do you want to split it to per-package files?

**Before** - one huge `config/config_dev.php` with many extensions in a single file:

```php
<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('framework', [
        'secret' => '%env(APP_SECRET)%',
        'test' => true,
    ]);

    $containerConfigurator->extension('doctrine', [
        'dbal' => [
            'url' => '%env(DATABASE_URL)%',
        ],
    ]);

    $containerConfigurator->extension('monolog', [
        'handlers' => [
            'main' => [
                'type' => 'stream',
                'path' => '%kernel.logs_dir%/%kernel.environment%.log',
            ],
        ],
    ]);
};
```

Run the command:

```bash
vendor/bin/swiss-knife split-config-per-package config/config_dev.php --output-dir config/packages/dev
```

**After** - the original config only imports the per-package files:

```php
<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->import(__DIR__ . '/packages/dev/*');
};
```

And each extension lives in its own file, e.g. `config/packages/dev/doctrine.php`:

```php
<?php

declare(strict_types=1);

return static function (Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('doctrine', [
        'dbal' => [
            'url' => '%env(DATABASE_URL)%',
        ],
    ]);
};
```

All the extensions will be extracted to separate files in `config/packages/dev` directory, making them much more readable.

<br>

## 7. Generate Symfony Smoke Tests

Cover your Symfony app with smoke tests in seconds. This command scans your `composer.json`, picks the matching test templates, and drops them under `tests/Unit/Smoke` (or your project's equivalent unit-tests directory).

```bash
vendor/bin/swiss-knife generate-symfony-smoke-tests
```

The command will:

* detect your unit tests directory and create a `Smoke` sub-directory
* generate a `ServiceContainerTest` that boots the kernel and instantiates every service to catch container misconfiguration early
* add a shared `AbstractContainerTestCase` with a typed `getService()` helper
* adjust the namespace and `Kernel` class in the templates to match your project (uses `App\Kernel`, `AppKernel`, or `Kernel`, whichever exists)

Existing files are never overwritten, so the command is safe to re-run.

The generated `ServiceContainerTest` boots the kernel and asserts every service can be instantiated:

```php
<?php

declare(strict_types=1);

namespace App\Tests\Unit\Smoke;

use Throwable;

final class ServiceContainerTest extends AbstractContainerTestCase
{
    public function testServiceConstruction(): void
    {
        $serviceIds = self::$container->getServiceIds();

        $checkedServiceCount = 0;

        foreach ($serviceIds as $serviceId) {
            if ($this->isDynamicService($serviceId)) {
                continue;
            }

            try {
                self::$container->get($serviceId);
            } catch (Throwable $throwable) {
                $this->fail(sprintf('Service "%s" could not be created because:%s%s', $serviceId, PHP_EOL, $throwable->getMessage()));
            }

            ++$checkedServiceCount;
        }

        // @todo update this number to match your service count
        $this->assertSame(100000, $checkedServiceCount);
    }

    private function isDynamicService(string $serviceId): bool
    {
        if (str_contains($serviceId, 'session')) {
            return true;
        }

        if (str_starts_with($serviceId, 'doctrine.')) {
            return true;
        }

        return in_array(
            $serviceId,
            ['kernel', 'database_connection', 'event_dispatcher'],
            true
        );
    }
}
```

<br>

## 8. Detect Duplicated Code

Spot copy-pasted code blocks with a token-based detector, a small clone of phpcpd.
Add it to CI to fail when a large copy-pasted block is added:

```bash
vendor/bin/swiss-knife duplicated-code src
vendor/bin/swiss-knife duplicated-code src rules --min-tokens 150 --min-lines 5
```

Options:

- `--min-lines` minimum lines of a reported clone (default `5`)
- `--min-tokens` minimum tokens of a reported clone (default `70`)
- `--fuzzy` ignore variable names when matching
- `--skip-file` file paths or masks to skip

Exit code is `1` when clones are found, `0` otherwise.

<br>

## 10. Measure Code Size

Measure lines of code and structure size of your project - files, classes, methods, constants and more:

```bash
vendor/bin/swiss-knife measure src
vendor/bin/swiss-knife measure src tests --exclude tests/fixtures
```

Options:

- `--exclude` paths to exclude
- `--short` print short metrics only
- `--longest` show top 10 longest files
- `--allow-vendor` allow the `/vendor` directory to be scanned
- `--json` output in JSON format

<br>

## 11. Count PHP Features

Count PHP features used in the project, grouped by the PHP version that introduced them.
Handy to see how modern your code is before an upgrade:

```bash
vendor/bin/swiss-knife features src
```

Options:

- `--json` output in JSON format

<br>

## 12. Check for Outdated Dependencies in CI

Postponing upgrades leads to large, risky jumps. The `breakpoint` command catches **outdated major packages** early, right in your CI pipeline:

```bash
vendor/bin/swiss-knife breakpoint
```

↓

<img src="/docs/breakpoint.png" alt="Breakpoint" width="600">

<br>

If there are more than 5 major outdated packages, the **CI will fail**.

Options:

- `--limit` raise or lower the bar, e.g. `--limit 3`
- `--dev` check dev packages only (safer to upgrade first)

<br>

## 13. Open up Next Versions

We know we're behind, but where to start? Instead of guessing, let Composer handle it - open up package versions to the next nearest step:

```bash
vendor/bin/swiss-knife open-versions
```

This opens up 5 versions to their next step, e.g.:

```diff
 {
     "require": {
         "php": "^7.4",
-        "symfony/console": "5.1.*"
+        "symfony/console": "5.1.*|5.2.*"
     },
     "require-dev": {
-        "phpunit/phpunit": "^9.0"
+        "phpunit/phpunit": "^9.0|^10.0"
     }
 }
```

Then run `composer update` - if no blockers exist, Composer updates packages to their next version.

Options:

- `--limit` number of packages to open, e.g. `--limit 3`
- `--package-prefix` upgrade only a group, e.g. `--package-prefix symfony`
- `--dev` low-risk dev packages first
- `--dry-run` preview changes without modifying `composer.json`

<br>

## 14. Raise to Installed Versions

Sometimes it's the opposite - dependencies are new, but `composer.json` is outdated:

<img src="/docs/composer-outdated-install.png" alt="Outdated composer.json" width="450">

Here `illuminate/container` allows 12.0 but we already use 12.14, and `symfony/finder` allows 6.4 but we use 7.2. Running `composer update` could pull unnecessary older dependencies.

Raise `composer.json` to the installed versions:

```diff
 {
     "require": {
         "php": "^7.4",
-        "illuminate/container": "^12.0",
+        "illuminate/container": "^12.14",
-        "symfony/finder": "^6.4|^7.2",
+        "symfony/finder": "^7.2"
     }
 }
```

```bash
vendor/bin/swiss-knife raise-to-installed
```

Options:

- `--dry-run` preview changes without applying

<br>

That's it!

<br>

Happy coding!
