<?php

namespace WPStaticOptions;

use InvalidArgumentException;

class Resolver
{
    protected static $inFilter = false;

    public static function enterFilter()
    {
        static::$inFilter = true;
    }

    public static function exitFilter()
    {
        static::$inFilter = false;
    }

    public static function inFilter()
    {
        return static::$inFilter;
    }

    protected $options;

    protected $callback;

    public function __construct(Set $options, /* callable */ $callback = null)
    {
        $this->setOptions($options);

        if (!is_null($callback)) {
            $this->setCallback($callback);
            return;
        }

        $this->setCallback('\get_option');
    }

    public function getCallback()/*: callable */
    {
        return $this->callback;
    }

    public function setCallback(/* callable */ $callback)
    {
        if (!is_callable($callback, true)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Invalid callback passed to %s',
                    __CLASS__
                )
            );
        }

        $this->callback = $callback;
    }

    public function getOptions(): Set
    {
        return $this->options;
    }

    public function setOptions(Set $options)
    {
        $this->options = $options;
    }

    public function hasOption($name)
    {
        return $this->getOptions()->has($name);
    }

    public function getOption($name)
    {
        return $this->getOptions()->get($name);
    }

    public function getBackendOption($name, $default = null)
    {
        try {
            static::enterFilter();
            return call_user_func($this->getCallback(), $name, $default);
        } finally {
            static::exitFilter();
        }
    }

    public function fetch($previous, $name, $default = null)
    {
        // Prevent infinite filter loop
        if (static::inFilter()) {
            return false;
        }

        if (!$this->hasOption($name)) {
            return $previous;
        }

        return $this->resolve($name, $this->getOption($name), $default);
    }

    protected function resolve($name, $config, $default = null)
    {
        // For non serialized options, return the configured value if exists.
        if (!$this->isOptionsHash($config)) {
            return $config;
        }

        // For serialized options, get the actual value from database and merge with configuration.
        return $this->merge($this->getBackendOption($name, $default), $config);
    }

    protected function merge($options, $config)
    {
        foreach ($config as $key => $value) {
            if (isset($options[$key]) && $this->isOptionsHash($options[$key])) {
                $options[$key] = $this->merge($options[$key], $value);
                continue;
            }

            $options[$key] = $value;
        }

        return $options;
    }

    public function mergeAll($options)
    {
        return array_reduce(
            $names = array_keys($options),
            function ($merged, $name) use ($options) {
                if (!$this->hasOption($name)) {
                    $merged[$name] = $options[$name];
                    return $merged;
                }

                if (!$this->isOptionsHash($options[$name])) {
                    $merged[$name] = $this->getOption($name);
                    return $merged;
                }

                $merged[$name] = $this->merge($options[$name], $this->getOption($name));
                return $merged;
            },
            $this->getOptions()->all()
        );
    }

    protected function isOptionsHash($options)
    {
        return is_array($options) && count(array_filter(array_keys($options), 'is_string')) > 0;
    }
}
