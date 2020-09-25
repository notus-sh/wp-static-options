<?php

/**
 * Setup auto-loading
 */

require_once realpath(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php');

/**
 * Set test environment constants
 */
define('TESTS_ROOT', realpath(__DIR__));
define('TESTS_DATA', realpath(TESTS_ROOT . DIRECTORY_SEPARATOR . 'data'));
