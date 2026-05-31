<?php
namespace App\Core;

/**
 * Global helper functions for the LMS application.
 *
 * Loaded once at application bootstrap via public/index.php.
 */

/**
 * Safely escape a string for HTML output (XSS prevention).
 *
 * @param  string|null $string  The raw string to escape.
 * @return string               The HTML-safe escaped string.
 */
function e(?string $string): string {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Dump a value and die (for quick debugging only — do not commit).
 *
 * @param  mixed $data  The value to inspect.
 * @return never
 */
function dd(mixed $data): never {
    echo '<pre style="background:#1e1e2e;color:#cdd6f4;padding:1rem;border-radius:8px;font-size:14px;">';
    var_dump($data);
    echo '</pre>';
    die();
}
