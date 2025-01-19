# Swiss Knife for Upgrades

[![Downloads total](https://img.shields.io/packagist/dt/rector/swiss-knife.svg?style=flat-square)](https://packagist.org/packages/rector/swiss-knife/stats)

Swiss knife in pocket of every upgrade architect!

<br>

## Install

```bash
composer require rector/swiss-knife --dev
```

## Usage

### 1. Check your Code for Git Merge Conflicts

Do you use Git? Then merge conflicts is not what you want in your code ever to see in pushed code:

```bash
<<<<<<< HEAD
````

Add this command to CI to spot these:

```bash
vendor/bin/swiss-knife check-conflicts .
```

<br>

### 2. Detect Commented Code

Have you ever forgot commented code in your code?

```php
//      foreach ($matches as $match) {
//           $content = str_replace($match[0], $match[2], $content);
//      }
```

No more! Add this command to CI to spot these:

```bash
vendor/bin/swiss-knife check-commented-code <directory>
vendor/bin/swiss-knife check-commented-code packages --line-limit 5
```

<br>

### 3. Reach full PSR-4

#### Find multiple classes in single file

To make PSR-4 work properly, each class must be in its own file. This command makes it easy to spot multiple classes in single file:

```bash
vendor/bin/swiss-knife find-multi-classes src
```

<br>

#### Update Namespace to match PSR-4 Root

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

### 4. Finalize classes without children

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

<br>

### 5. Privatize local class constants

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

### 6. Mock only constructor param you need with MockWire

Imagine there is a service that has 6 dependencies in `__construct()`:

```php
final class RealClass
{
    public function __construct(
        private readonly FirstService $firstService,
        private readonly SecondService $secondService,
        private readonly ThirdService $thirdService,
        private readonly FourthService $fourthService,
        private readonly FifthService $fifthService,
        private readonly SixthService $sixthService
    ) {
    }
}
```

<br>

But we want to **mock only one of them**:

```php
use Rector\SwissKnife\Testing\MockWire;

// pass a mock
$thirdDependencyMock = $this->createMock(ThirdDependency::class);
$thirdDependencyMock->method('someMethod')->willReturn('some value');

$realClass = MockWire::create(RealClass::class, [
    $thirdDependencyMock
]);
```

<br>

Or pass direct instance:

```php
$realClass = MockWire::create(RealClass::class, [
    new ThirdDependency()
]);
```

The rest of argument will be mocked automatically.

This way we:

* can easily **change the class constructor**, without having burden of changing all the tests.
* see what is really being used in the constructor
* avoid any mock-mess clutter properties all over our test

<br>

## 7. Find unused Behat definitions with static analysis - Behastan

Behat uses `@When()`, `@Then()` and `@Given()` annotations and their PHP 8 attribute alternatives to define method to be called in `*.feature` files. Sometimes test change and lines from `*.feature` files are deleted. But what about definitions?

This command helps you to spot definitions that are no longer needed. Just provide test directory (1 or more) and let it statically compare defined and used masks:

```bash
vendor/bin/swiss-knife behastan tests
```

↓

```bash
Checking static, named and regex masks from 100 *Feature files
==============================================================

Found 1036 masks:

 * 747 exact
 * 106 /regex/
 * 181 :named

 1036/1036 [▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓] 100%

the product price is :value
tests/Behat/ProductContext.php

/^I submit order form and see payment page$/
tests/Behat/OrderContext.php


 [ERROR] Found 2 unused definitions
```

You can also add this command to CI, to get instant feedback about unused definitions.

<br>

## 8. Quick search PHP files with regex

Data beats guess. Do you need a quick idea how many files contain `$this->get('...')` calls? Or another anti-pattern you want to remove?

PhpStorm helps with similar search, but stops counting at 100+. To get exact data about your codebase, use this command:

```bash
vendor/bin/swiss-knife search-regex "#this->get\((.*)\)#"
```

↓

```bash
Going through 1053 *.php files
Searching for regex: #this->get\((.*)\)#

 1053/1053 [▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓] 100%

 * src/Controller/ProjectController.php: 15
 * src/Controller/OrderController.php: 5


 [OK] Found 20 cases in 2 files

```


<br>

## 9. Convert Alice fixtures from YAML to PHP

The `nelmio/alice` package [allows to use PHP](https://github.com/nelmio/alice/blob/v2.3.0/doc/complete-reference.md#php) for test fixture definitions. It's much better format, because Rector and PHPStan can understand it.

But what if we have 100+ YAML files in our project?

```bash
vendor/bin/swiss-knife convert-alice-yaml-to-php fixtures
```

That's it!

<br>

## 10. Generate Symfony 5.3 configs builders

Symfony 5.3 introduced amazing [config builders](https://symfony.com/blog/new-in-symfony-5-3-config-builder-classes), but those classes are not available for IDE until used. To make it easier, we added a command that generates all config builder classes you project can use, in `/var/cache/Symfony`.

```bash
vendor/bin/swiss-knife generate-symfony-config-builders
```

That way IDE, PHPStan after adding those paths and Rector can understand your config files better.

<br>

## 11. Spots Fake Traits

What is trait has 5 lines and used in single service? We know it's better to e inlined, to empower IDE, Rector and PHPStan. But don't have time to worry about these details.

We made a command to automate this process and spot the traits most likely to be inlined:

```bash
vendor/bin/swiss-knife spot-lazy-traits src
```

<br>

By default, the commands look for traits used max 2-times. To change that:

```bash
vendor/bin/swiss-knife spot-lazy-traits src --max-used 4
```

That's it! Run this command once upon a time or run it in CI to eliminate traits with low value to exists. Your code will be more robuts and easier to work with.


<br>

## 12. Mutual Private Repository Dependencies

Do you have a project that uses 5 private repositories? And another one that uses different 3 private repositories? Are their open-source dependencies up to date?

In such situations, we want to get a quick idea about the weakest link. Do all those repositories use the same version of "symfony/console"? What about their min PHP versions?

<br>

That's where `multi-package-composer-stats` comes. Feed it the links to dependency repositories you want to know versions for:

```bash
vendor/bin/swiss-knife multi-package-composer-stats private-company/private-repository.git \
    private-company/another-private-repository.git
```

↓

<kbd>
    <img src="https://pbs.twimg.com/media/GhfcUmoXkAAFA1S?format=png" style="max-width:15em">
</kbd>

<br>
<br>
<br>

Do you want to compare **the main projects** and themselves and fetch their dependencies automatically? Provide urls to the repositories and the `--is-source` option:

```bash
vendor/bin/swiss-knife multi-package-composer-stats private-company/first-project.git \
    private-company/another-project.git
```

Now all the dependencies are fetched per provided project from the `"repositories"` section of `composer.json`.

That's it!

<br>

Happy coding!
