<?php
namespace App\Core;

/**
 * Class Request
 * 
 * Abstraction layer for HTTP request variables.
 */
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
Class_Alias('App\\Core\\Request', 'Request');
