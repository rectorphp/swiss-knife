# Easy CI

[![Downloads total](https://img.shields.io/packagist/dt/symplify/easy-ci.svg?style=flat-square)](https://packagist.org/packages/symplify/easy-ci/stats)

Tools that make easy to setup CI.

- Check git conflicts in CI

## Install

```bash
composer require symplify/easy-ci --dev
```

## Usage

### 1. Check your Code for Git Merge Conflicts

Do you use Git? Then merge conflicts is not what you want in your code ever to see:

```bash
<<<<<<< HEAD
this is some content to mess with
content to append
=======
totally different content to merge later
````

How to avoid it? Add check to your CI:

```bash
vendor/bin/easy-ci check-conflicts .
```

The `/vendor` directory is excluded by default.

<br>

### 2. Detect Commented Code

Have you ever forgot commented code in your code?

```php
//      foreach ($matches as $match) {
//           $content = str_replace($match[0], $match[2], $content);
//      }
```

Clutter no more! Add `check-commented-code` command to your CI and don't worry about it:

```bash
vendor/bin/easy-ci check-commented-code <directory>
vendor/bin/easy-ci check-commented-code packages --line-limit 5
```

<br>

### 3. Find multiple classes in single file

To make PSR-4 work properly, each class must be in its own file. This command makes it easy to spot multiple classes in single file:

```bash
vendor/bin/easy-ci find-multi-classes src
```

<br>

### 4. Update Namespace to match PSR-4 Root

Is your class in wrong namespace? Make it match your PSR-4 root:

```bash
vendor/bin/easy-ci namespace-to-psr-4 src --namespace-root "App\\"
```

This will update all files in your `/src` directory, to starts with `App\\` and follow full PSR-4 path:

```diff
 # file path: src/Repository/TalkRepository.php

-namespace Model;
+namespace App\Repository;

 ...
```

<br>

## Report Issues

In case you are experiencing a bug or want to request a new feature head over to the [Symplify monorepo issue tracker](https://github.com/symplify/symplify/issues)

## Contribute

The sources of this package are contained in the Symplify monorepo. We welcome contributions for this package on [symplify/symplify](https://github.com/symplify/symplify).
