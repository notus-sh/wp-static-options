<?php

namespace WPStaticOptions\Tests\Integration;

use Error;
use PHPUnit\Framework\TestCase;
use RuntimeException;

use function WPStaticOptions\setup;

class GetOptionTest extends TestCase
{
    public function setUp(): void
    {
        if (false === $abspath = realpath(TESTS_ROOT . '/../vendor/roots/wordpress/')) {
            throw new RuntimeException("roots/wordpress is not installed");
        }
        
        defined('ABSPATH') || define('ABSPATH', $abspath . DIRECTORY_SEPARATOR);
        defined('WPINC') || define('WPINC', 'wp-includes');
    
        require_once ABSPATH . WPINC . '/load.php';
        require_once ABSPATH . WPINC . '/plugin.php';
        require_once ABSPATH . WPINC . '/cache.php';
        require_once ABSPATH . WPINC . '/option.php';
        
        wp_installing(false);
    }
    
    public function testGetOption()
    {
        setup(TESTS_DATA . '/config/setup');
        
        $this->assertEquals('c', get_option('date_format'));
        $this->assertEquals('https://example.com', get_option('home'));
    
        $this->expectException(Error::class);
        get_option('show_avatars');
    }
}
