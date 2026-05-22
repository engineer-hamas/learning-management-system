<?php
namespace App\Core;

/**
 * Custom PSR-4 Autoloader
 * 
 * Maps App\ namespaces to the src/ directory.
 */
class Autoloader {
    /**
     * Register the autoloader function with spl_autoload_register
     */
    public static function register(): void {
        spl_autoload_register(function (string $class) {
            // Project-specific namespace prefix
            $prefix = 'App\\';

            // Base directory for the namespace prefix (points to src/)
            $baseDir = dirname(__DIR__) . '/';

            // Does the class use the namespace prefix?
            $len = strlen($prefix);
            if (strncmp($prefix, $class, $len) !== 0) {
                // No, move to the next registered autoloader
                return;
            }

            // Get the relative class name
            $relativeClass = substr($class, $len);

            // Replace namespace separators with directory separators in the relative
            // class name, append with .php
            $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

            // If the file exists, require it
            if (file_exists($file)) {
                require_once $file;
            }
        });
    }
}
Class_Alias('App\\Core\\Autoloader', 'Autoloader');
