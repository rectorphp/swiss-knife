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
vendor/bin/swiss-knife finalize-classes src tests
```

Do you use mocks but not [bypass final](https://tomasvotruba.com/blog/2019/03/28/how-to-mock-final-classes-in-phpunit) yet?

```bash
vendor/bin/swiss-knife finalize-classes src tests --skip-mocked
```

This will keep mocked classes non-final, so PHPUnit can extend them internally.

<br>

### 5. Privatize local class constants

PHPStan can report unused private class constants, but it skips all the public ones.
Do you have lots of class constants, all of them public but want to narrow scope to privates?

```bash
vendor/bin/swiss-knife privatize-constants src
```

This command will:

* make all constants private
* runs PHPStan to find out, which of them are used
* restores only the used constants back to `public`

That way all the constants not used outside will be made `private` safely.

<br>

Happy coding!
