<?php

namespace WPStaticOptions;

use Noodlehaus\Config;
use Webmozart\Glob\Glob;

class Loader extends Config
{
    /**
     * @param array|string $paths
     * @return Config
     */
    public static function loadRecursive($paths): Config
    {
        return self::load(self::globFiles(self::existingPaths((array) $paths)));
    }
    
    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected static function globFiles(array $paths): array
    {
        $all = array_reduce($paths, function ($globs, $path) {
            if (is_dir($path)) {
                return array_merge($globs, Glob::glob($path . '/**/*'));
            }
            
            return array_merge($globs, [$path]);
        }, []);
        
        return array_filter($all, function ($path) {
            return !is_dir($path);
        });
    }
    
    protected static function existingPaths(array $paths): array
    {
        return array_filter($paths, function ($path) {
            return false !== realpath($path);
        });
    }
}
