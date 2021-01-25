<?php

namespace WPStaticOptions\Tests\Unit;

use PHPUnit\Framework\TestCase;
use RuntimeException;

use function WPStaticOptions\setup;

class SetupTest extends TestCase
{
    public function testSetup()
    {
        $stub = $this->createStub(self::class);
        $stub
            ->expects($this->exactly(3))->method('stubbed')
            ->with($this->stringStartsWith('pre_'))
            ->will($this->returnValue(null));

        setup(TESTS_DATA . '/config/setup', [$stub, 'stubbed']);
    }

    public function stubbed()
    {
        throw new RuntimeException("Original method should not have been called");
    }
}
