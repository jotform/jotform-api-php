<?php
/**
 * Jotform API - PHP Client
 *
 * @copyright   2021 Jotform, LLC.
 * @link        https://www.jotform.com
 * @version     2.0
 * @package     Jotform
 */

spl_autoload_register(function ($class) {
    $file = array_values(explode('\\', $class));
    $class = end($file);
    $paths = [
        __DIR__ . '/src',
        __DIR__ . '/src/Services',
        __DIR__ . '/src/Traits',
        __DIR__ . '/src/Exceptions',
    ];
    foreach ($paths as $path) {
        if (is_readable($file = "{$path}/{$class}.php")) {
            require_once($file);
        }
    }
});
