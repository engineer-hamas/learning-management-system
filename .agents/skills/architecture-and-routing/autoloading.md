# Autoloading Subskill

This subskill defines the lightweight custom PSR-4 Autoloader specification that registers all classes in the `src/` directory.

## Implementation Details

We use `App\Core\Autoloader` located in `src/Core/Autoloader.php`. This removes the need for manual `require_once` statements.

### Autoloader Source Specification

The autoloader maps the prefix `App\` to the physical `src/` directory. When class `App\Core\Router` is called, it parses:
1. `App\` -> `src/`
2. `Core\Router` -> `Core/Router.php`
3. Result -> `src/Core/Router.php`

```php
<?php
namespace App\Core;

class Autoloader {
    /**
     * Register the custom PSR-4 loader
     */
    public static function register(): void {
        spl_autoload_register(function ($class) {
            $prefix = 'App\\';
            $baseDir = dirname(__DIR__) . '/';

            // Check if class uses namespace prefix
            $len = strlen($prefix);
            if (strncmp($prefix, $class, $len) !== 0) {
                return;
            }

            // Get the relative class name
            $relativeClass = substr($class, $len);

            // Replace namespace separators with directory separators and append .php
            $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

            if (file_exists($file)) {
                require_once $file;
            }
        });
    }
}
```

## How to Register a New Class

When creating any new PHP class:
1. Declare the namespace matching its folder location under `src/` (e.g. `namespace App\Models;` for files in `src/Models/`).
2. Do not use manual `require` or `include` to load this class in other code files.
3. Call it with its fully-qualified name or import it via a `use` statement:
   ```php
   use App\Core\Request;
   use App\Controllers\AuthController;
   ```
4. Ensure the file name matches the class name exactly (case-sensitive!).

### Autoloader Initializer
The autoloader is initialized once at the very start of the front-controller `public/index.php`:
```php
require_once __DIR__ . '/../src/Core/Autoloader.php';
\App\Core\Autoloader::register();
```
