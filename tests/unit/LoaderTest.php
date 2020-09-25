<?php

namespace WPStaticOptions\Tests\Unit;

use WPStaticOptions\Loader;
use PHPUnit\Framework\TestCase;

class LoaderTest extends TestCase
{
    public function testLoadRecursiveFromSinglePath()
    {
        $loaded = Loader::loadRecursive(TESTS_DATA . '/config/loader/simple');

        $this->assertTrue($loaded->get('top'));
        $this->assertTrue($loaded->get('deep'));
        $this->assertTrue($loaded->get('merged.fromPhp'));
        $this->assertEmpty(array_diff(array_keys($loaded->get('merged')), ['fromPhp', 'fromYaml']));
    }

    public function testLoadRecursiveFromMultiplePaths()
    {
        $paths = [
            TESTS_DATA . '/config/loader/simple',
            TESTS_DATA . '/config/loader/multiple',
            TESTS_DATA . '/config/loader/multiple.yml'
        ];
        $loaded = Loader::loadRecursive($paths);

        $this->assertEqualsCanonicalizing(
            ['fromPhp', 'fromYaml', 'fromIni', 'fromTooFar', 'fromJson', 'fromAFile'],
            array_keys($loaded->get('merged'))
        );
    }

    public function testLoadRecursiveOrder()
    {
        $paths = [
            TESTS_DATA . '/config/loader/simple',
            TESTS_DATA . '/config/loader/multiple'
        ];
        $loaded = Loader::loadRecursive($paths);

        $this->assertEquals([
            // From simple
            'fromYaml',
            'fromPhp',
            // From multiple
            'fromJson',
            'fromIni',
            'fromTooFar'
        ], array_keys($loaded->get('merged')));
    }

    public function testLoadRecursiveWithMissingPath()
    {
        $paths = [
            TESTS_DATA . '/config/loader/simple',
            TESTS_DATA . '/config/loader/missing'
        ];
        $loaded = Loader::loadRecursive($paths);

        $this->assertEmpty(array_diff(array_keys($loaded->get('merged')), ['fromPhp', 'fromYaml']));
    }
}
