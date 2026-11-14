# Modern WPCS Standard ![CI](https://github.com/contactjavas/modern-wpcs-standard/actions/workflows/ci.yml/badge.svg)

These are a set of modern (PHP >= 8.4) linting guidelines for WordPress development. Because of the newer PHP version, it is not suitable for work on Core WordPress, but may be useful for those who are not bound by legacy PHP versions.

These guidelines are being developed primarily for [contactjavas](https://github.com/contactjavas), but anyone is free to use them, suggest changes, or report bugs.

This project is a [phpcs](https://github.com/squizlabs/PHP_CodeSniffer) "standard" (a collection of rules or "sniffs") that can be included in any project.

## Installation

To use these rules in a project which is set up using [composer](https://getcomposer.org/), we recommend using the [phpcodesniffer-composer-installer library](https://github.com/PHPCSStandards/composer-installer) which will automatically use all installed standards in the current project with the composer type `phpcodesniffer-standard` when you run phpcs.

```
composer require --dev squizlabs/php_codesniffer dealerdirect/phpcodesniffer-composer-installer
composer require --dev contactjavas/modern-wpcs-standard
```

If you want this standard, the WordPress standard, the VariableAnalysis standard, and other customizations, you can install the meta-standard [ModernWpcsRuleset](https://github.com/contactjavas/modern-wpcs-ruleset) instead.

```
composer require --dev squizlabs/php_codesniffer dealerdirect/phpcodesniffer-composer-installer
composer require --dev contactjavas/modern-wpcs-ruleset
```

## Configuration

When installing sniff standards in a project, you edit a `phpcs.xml` file with the `rule` tag inside the `ruleset` tag. The `ref` attribute of that tag should specify a standard, category, sniff, or error code to enable. It's also possible to use these tags to disable or modify certain rules. The [official annotated file](https://github.com/squizlabs/PHP_CodeSniffer/wiki/Annotated-ruleset.xml) explains how to do this.

```xml
<?xml version="1.0"?>
<ruleset name="MyStandard">
 <description>My library.</description>
 <rule ref="ModernWpcsStandard"/>
</ruleset>
```

## Usage

Most editors have a phpcs plugin available, but you can also run phpcs manually. To run phpcs on a file in your project, just use the command-line as follows (the `-s` causes the sniff code to be shown, which is very important for learning about an error).

```
vendor/bin/phpcs -s src/MyProject/MyClass.php
```

-----

# Guidelines

View the Guidelines [here](./GUIDELINES.md).

