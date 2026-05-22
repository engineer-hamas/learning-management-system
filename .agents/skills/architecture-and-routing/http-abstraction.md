# HTTP Abstraction Subskill

This subskill governs Request and Response object abstractions, which encapsulate interaction with global PHP state (`$_GET`, `$_POST`, `$_SERVER`, body payloads, headers, output buffers, redirects, and session views).

## Request API (`src/Core/Request.php`)

The `Request` object captures all request parameters and methods cleanly:

```php
<?php
namespace App\Core;

class Request {
    private array $getParams;
    private array $postParams;
    private string $method;
    private string $uri;

    public function __construct() {
        $this->getParams = $_GET;
        $this->postParams = $_POST;
        $this->method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $this->uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    }

    public function getMethod(): string {
        return $this->method;
    }

    public function getUri(): string {
        return $this->uri;
    }

    public function isPost(): bool {
        return $this->method === 'POST';
    }

    public function isGet(): bool {
        return $this->method === 'GET';
    }

    /**
     * Retrieve parameter from GET payload
     */
    public function get(string $key, $default = null) {
        return $this->getParams[$key] ?? $default;
    }

    /**
     * Retrieve parameter from POST payload
     */
    public function post(string $key, $default = null) {
        return $this->postParams[$key] ?? $default;
    }

    /**
     * Retrieve clean data fields from POST (trim whitespace)
     */
    public function cleanPost(string $key, string $default = ''): string {
        return isset($this->postParams[$key]) ? trim((string)$this->postParams[$key]) : $default;
    }

    /**
     * Get all request parameters
     */
    public function all(): array {
        return array_merge($this->getParams, $this->postParams);
    }
}
```

## Response API (`src/Core/Response.php`)

The `Response` class is responsible for sending headers, rendering view templates from `src/Views/`, returning JSON objects, and executing redirects:

```php
<?php
namespace App\Core;

class Response {
    /**
     * Render an HTML view
     */
    public function render(string $view, array $data = []): void {
        // Extract variables to be available within the scope of the required view file
        extract($data);

        $viewFile = dirname(__DIR__) . "/Views/{$view}.php";

        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            trigger_error("View file not found: {$viewFile}", E_USER_ERROR);
        }
        exit;
    }

    /**
     * Perform HTTP redirect
     */
    public function redirect(string $url): void {
        header("Location: {$url}");
        exit;
    }

    /**
     * Send a JSON response
     */
    public function json(array $data, int $statusCode = 200): void {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($statusCode);
        echo json_encode($data);
        exit;
    }
}
```
