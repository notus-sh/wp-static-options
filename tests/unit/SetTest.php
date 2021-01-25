<?php

namespace WPStaticOptions\Tests\Unit;

use PHPUnit\Framework\TestCase;
use WPStaticOptions\Set;

class SetTest extends TestCase
{
    public function testLoadingFromDirectory()
    {
        $set = Set::from(TESTS_DATA . '/config/loader/simple');

        $this->assertTrue($set->get('top'));
        $this->assertTrue($set->get('deep'));
        $this->assertTrue($set->get('merged.fromPhp'));
        $this->assertEmpty(array_diff(array_keys($set->get('merged')), ['fromPhp', 'fromYaml']));
    }

    public function testKeys()
    {
        $set = Set::from(TESTS_DATA . '/config/loader/simple');

        $this->assertEmpty(array_diff($set->keys(), ['top', 'deep', 'merged']));
    }

    public function testFromSingletonUnicity()
    {
        $setA = Set::from(TESTS_DATA . '/config/loader/simple');
        $setB = Set::from(TESTS_DATA . '/config/loader/simple');

        $this->assertSame($setA, $setB);
    }

    public function testFromSingletonDistinct()
    {
        $setA = Set::from(TESTS_DATA . '/config/loader/simple');
        $setB = Set::from(TESTS_DATA . '/config/loader/multiple');

        $this->assertNotSame($setA, $setB);
    }
}
