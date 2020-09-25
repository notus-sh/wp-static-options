# wp-static-options

A WordPress plugin **for developers** to force strategic WordPress options through configuration files. 

## Why `wp-static-options`?

[WordPress](https://wordpress.org/) is well-known to be customizable through dozens of options and most plugins try to offer the same level of configurability. All options can be edited through WordPress Administration in a more or less organized and comprehensive way, depending on how many plugins you have, how complex they are and how thorough were plugin developers to follow [WordPress administration's guidelines](https://developer.wordpress.org/plugins/settings/).

It's great **and** it's a pain. Great because it allows people who know nothing about web technologies to publish their website (sometimes after some headaches). A pain because, as a project grows, you'll certainly end with some options that must definitely not be changed unless you want its public part to fall apart (or, at least, behave in unexpected ways).

Need examples? Just think about WordPress permalinks or WooCommerce payment gateways options and I think you'll get the point.

## What does `wp-static-options`?

`wp-static-options` allows you to set options once and for all in configuration files. It hooks into [WordPress's `get_option`](https://developer.wordpress.org/reference/functions/get_option/) to always return the right value (the one you want to be set).



## Contributing

### Development environment

You can use `dev/docker/Dockerfile` to build a lightweight local development environment.

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
