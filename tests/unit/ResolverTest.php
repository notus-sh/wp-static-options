<?php

namespace WPStaticOptions\Tests\Unit;

use Exception;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use WPStaticOptions\Resolver;
use WPStaticOptions\Set;

class ResolverTest extends TestCase
{
    public function testSemaphore()
    {
        Resolver::enterFilter();
        $this->assertTrue(Resolver::inFilter());

        Resolver::exitFilter();
        $this->assertFalse(Resolver::inFilter());
    }

    public function testHasOption()
    {
        $set = Set::from(TESTS_DATA . '/config/resolver');
        $resolver = new Resolver($set);

        $this->assertTrue($resolver->hasOption('string'));
        $this->assertFalse($resolver->hasOption('missing'));
    }

    public function testGetOption()
    {
        $set = Set::from(TESTS_DATA . '/config/resolver');
        $resolver = new Resolver($set);

        $this->assertEquals($resolver->getOption('string'), $set->get('string'));
    }

    public function testGetBackendOption()
    {
        $this->assertFalse(Resolver::inFilter());

        $resolver = new Resolver(
            new Set([]),
            function ($name, $default) {
                $this->assertTrue(Resolver::inFilter());
                $this->assertEquals('name', $name);
                $this->assertEquals('default', $default);
            }
        );
        $resolver->getBackendOption('name', 'default');

        $this->assertFalse(Resolver::inFilter());
    }

    public function testGetBackendOptionWithInvalidCallback()
    {
        $this->expectException(InvalidArgumentException::class);
        new Resolver(new Set([]), ['not', 'a', 'callback']);
    }

    public function testGetBackendOptionWithExceptionInsideCallback()
    {
        $resolver = new Resolver(
            new Set([]),
            function () {
                throw new RuntimeException('Oops!');
            }
        );

        $this->assertFalse(Resolver::inFilter());
        try {
            $resolver->getBackendOption('name', 'default');
        } catch (Exception $e) {
            /** Did someone said "Oops!"? */
        }
        $this->assertFalse(Resolver::inFilter());
    }

    public function testFetchMissingOption()
    {
        $resolver = new Resolver(
            new Set([]),
            function () {
                throw new RuntimeException('Callback should not have been called.');
            }
        );

        $this->assertEquals('pre', $resolver->fetch('pre', 'missing', 'default'));
    }

    public function testFetchSimpleOption()
    {
        $set = Set::from(TESTS_DATA . '/config/resolver');
        $resolver = new Resolver(
            $set,
            function () {
                throw new RuntimeException('Callback should not have been called.');
            }
        );

        $this->assertEquals($set->get('string'), $resolver->fetch('previous', 'string', 'default'));
        $this->assertEquals($set->get('integer'), $resolver->fetch('previous', 'integer', 'default'));
        $this->assertEquals($set->get('array'), $resolver->fetch('previous', 'array', 'default'));
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function testFetchMergedOption()
    {
        $set = Set::from(TESTS_DATA . '/config/resolver');
        $resolver = new Resolver(
            $set,
            function ($name, $default) {
                return $default;
            }
        );

        $hash = [
            'nested' => [
                'option' => false,
                'conflicting' => 'replaced'
            ]
        ];
        $resolved = $resolver->fetch('previous', 'hash', $hash);

        $this->assertEqualsCanonicalizing(['string', 'integer', 'array', 'nested'], array_keys($resolved));

        $this->assertEqualsCanonicalizing(['option', 'config', 'conflicting'], array_keys($resolved['nested']));
        $this->assertEquals($hash['nested']['option'], $resolved['nested']['option']);
        $this->assertNotEquals($hash['nested']['conflicting'], $resolved['nested']['conflicting']);
        $this->assertTrue($resolved['nested']['config']);
    }

    public function testMergeAll()
    {
        $set = Set::from(TESTS_DATA . '/config/resolver');
        $resolver = new Resolver($set);

        $options = [
            'string' => 'not the right string',
            'hash' => [
                'nested' => [
                    'option' => false,
                    'conflicting' => 'replaced'
                ]
            ]
        ];
        $merged = $resolver->mergeAll($options);

        $this->assertEqualsCanonicalizing(['string', 'integer', 'array', 'hash'], array_keys($merged));
        $this->assertNotEquals($options['string'], $merged['string']);

        $optionsHash = $options['hash'];
        $mergedHash = $merged['hash'];
        $this->assertEqualsCanonicalizing(['string', 'integer', 'array', 'nested'], array_keys($mergedHash));

        $optionsNested = $optionsHash['nested'];
        $mergedNested = $mergedHash['nested'];
        $this->assertEqualsCanonicalizing(['option', 'config', 'conflicting'], array_keys($mergedNested));
        $this->assertEquals($optionsNested['option'], $mergedNested['option']);
        $this->assertNotEquals($optionsNested['conflicting'], $mergedNested['conflicting']);
        $this->assertTrue($mergedNested['config']);
    }
}
