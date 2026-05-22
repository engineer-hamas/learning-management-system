# Base Controller Subskill

This subskill outlines the implementation of controllers in our MVC architecture. All controllers must reside in the `src/Controllers/` directory, use the `App\Controllers` namespace, and extend the base `App\Core\Controller` class.

## Base Controller Interface (`src/Core/Controller.php`)

The abstract base controller wraps core operations like rendering, redirects, and json encoding into clean, readable inherited methods:

```php
<?php
namespace App\Core;

abstract class Controller {
    /**
     * Render an HTML view with optional data
     */
    protected function render(string $view, array $data = []): void {
        (new Response())->render($view, $data);
    }

    /**
     * Redirect to a target URL
     */
    protected function redirect(string $url): void {
        (new Response())->redirect($url);
    }

    /**
     * Send a JSON response payload
     */
    protected function json(array $data, int $statusCode = 200): void {
        (new Response())->json($data, $statusCode);
    }
}
```

## How to Implement a Concrete Controller

Here is a standard namespaced controller illustrating how to handle actions:

```php
<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;

class MyCustomController extends Controller {
    
    /**
     * Handle homepage display
     */
    public function show(Request $request, Response $response): void {
        // Business logic here...
        
        $this->render('my-view', [
            'title' => 'My Premium LMS Title',
            'records' => []
        ]);
    }
}
```
