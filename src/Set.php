<?php

namespace WPStaticOptions;

use Noodlehaus\AbstractConfig;

class Set extends AbstractConfig
{
    protected static $instances = [];

    public static function from(string $directory): self
    {
        if (!array_key_exists($directory, self::$instances)) {
            self::$instances[$directory] = new Set(Loader::loadRecursive($directory)->all());
        }

        return self::$instances[$directory];
    }

    public function keys()
    {
        return array_keys($this->data);
    }
}
