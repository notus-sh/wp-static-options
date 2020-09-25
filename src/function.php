<?php

namespace WPStaticOptions;

function setup(string $directory, $callback = '\add_filter')
{
    $resolver = new Resolver(Set::from($directory));
    foreach ($resolver->getOptions() as $name => $value) {
        $filter = sprintf('pre_option_%s', $name);
        call_user_func($callback, $filter, [$resolver, 'fetch'], 1, 3);
    }
}
