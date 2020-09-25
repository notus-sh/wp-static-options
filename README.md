# wp-static-options

A WordPress plugin **for developers** to force strategic WordPress options through configuration files. 

## Why `wp-static-options`?

[WordPress](https://wordpress.org/) is well-known to be customizable through dozens of options and most plugins try to offer the same level of configurability. All options can be edited through WordPress Administration in a more or less organized and comprehensive way, depending on how many plugins you have, how complex they are and how thorough were plugin developers to follow [WordPress administration's guidelines](https://developer.wordpress.org/plugins/settings/).

It's great **and** it's a pain. Great because it allows people who know nothing about web technologies to publish their website (sometimes after some headaches). A pain because, as a project grows, you'll certainly end with some options that must definitely not be changed unless you want its public part to fall apart (or, at least, behave in unexpected ways).

Need examples? Just think about WordPress permalinks or WooCommerce payment gateways options and I think you'll get the point.

## What does `wp-static-options`?

`wp-static-options` allows you to set options once and for all in configuration files. It hooks into [WordPress's `get_option`](https://developer.wordpress.org/reference/functions/get_option/) to always return the right value (the one you want to be set).

## Installation

### Using Composer

`wp-static-options` works better when combined with [`roots/bedrock`](https://github.com/roots/bedrock) or a similar Composer setup for WordPress with [`composer/installers`](https://github.com/composer/installers) :

```json5
{
  // […]
  "require": {
    "composer/installers": "^1.8"
  },
  "extra": {
    "installer-paths": {
      "web/app/mu-plugins/{$name}/": ["type:wordpress-muplugin"],
      "web/app/plugins/{$name}/": ["type:wordpress-plugin"],
      "web/app/themes/{$name}/": ["type:wordpress-theme"]
    }
  }
  // […],
}
```

Once your setup is correct, install this package via Composer:

```shell script
composer require notus.sh/wp-static-options
```

### From sources

Grab the latest tarball from [this repository releases' page](https://github.com/notus-sh/wp-static-options/releases) and extract it to your WordPress Must-Use plugins folder (defaults to `wp-content/mu-plugins`).

## Usage

### Define your configuration directory
  
`wp-static-options` expect a `STATIC_OPTIONS_DIR` constant to be defined and contain the path to your configuration directory as a string. You can define it in your `wp-config.php` (or `config/application.php` if you use Root's Bedrock).  

If you don't define your own, a default value for `STATIC_OPTIONS_DIR` will be set to `WP_CONTENT_DIR . '/config/'`.

### Write your configuration files

`wp-static-options` will recursively load configuration files from `STATIC_OPTIONS_DIR` and merge them. Any file format supported by [`hassankhan/config`](https://github.com/hassankhan/config) is accepted. Feel free to split your configuration in as many files as you need (ex: one per plugin) and organise them the way you like.

For scalar options (whose values are integers, strings or numerically indexed arrays), add a top-level key to your configuration file and set the value:

```yaml
timezone_string: 'Europe/Paris'
date_format: 'j F Y'
time_format: 'H:i'
```

Some plugins store their options as serialized arrays. You can set only values you really need to and let WordPress manage the others:

```php
return [
  'woocommerce_stripe_testmode' => [
    'testmode' => (WP_ENV === 'production' ? 'yes' : 'no')
  ],
];
```

## Caveats

Du to [the way WordPress handle the return of `pre_option_*` filters](https://github.com/WordPress/WordPress/blob/master/wp-includes/option.php#L81), false booleans are not valid configuration values.

## Contributing

### Development environment

You can use `dev/docker/Dockerfile` to build a lightweight local development environment with PHP 7.4 and Composer.

```shell script
docker build                  \
  --build-arg USER_ID=$(id -u) \
  --build-arg GROUP_ID=$(id -g) \
  --tag php:dev                  \
  --file ./dev/docker/Dockerfile  \
  ./dev/docker/
``` 

Then run the container with :

```shell script
docker run -it --rm --mount type=bind,source="$(pwd)"/,target=/home/runner/app --entrypoint /bin/bash php:dev
```

### Available Composer scripts

Please use the available Composer scripts to validate your changes.

* `composer run tests`: Run unit and integration tests ([PHPUnit](https://github.com/sebastianbergmann/phpunit))
* `composer run tests-unit`: Run only unit tests
* `composer run tests-unit`: Run only integration tests
* `composer run lint`: Lint PHP code with [PHP CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer)
* `composer run phpmd`: Inspect PHP code with [PHP Mess Detector](https://github.com/phpmd/phpmd)
