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

*Note: The `/vendor` directory is excluded by default.*

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
vendor/bin/swiss-knife finalize-classes src
```

<br>

### 5. Dependency tools speed testing

Do you want to test speed of your dependency tools? E.g. if PHPStan or Rector got slower after upgrade?

1. Prepare a script in `composer.json`

```json
{
    "scripts": {
        "phpstan": "vendor/bin/phpstan analyse src --level 8"
    }
}
```

2. Run past X versions and measure time and memory


```bash
vendor/bin/swiss-knife speed-run-tool phpstan/phpstan --script-name phpstan --run-count 5
```

<br>

Happy coding!
