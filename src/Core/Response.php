<?php
namespace App\Core;

/**
 * Class Response
 * 
 * Abstraction layer for HTTP responses, rendering, and redirections.
 */
class Response {
    /**
     * Render an HTML view from the src/Views directory
     * 
     * @param string $view Name of the view file (without extension)
     * @param array $data Associative array of parameters to extract
     */
    public function render(string $view, array $data = []): void {
        // Extract variables to the local scope of this function
        extract($data);

        // Calculate absolute path to view template
        $viewFile = dirname(__DIR__) . "/Views/{$view}.php";

        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            trigger_error("View file not found: {$viewFile}", E_USER_ERROR);
        }
        exit;
    }

    /**
     * Perform an HTTP redirection and terminate execution
     * 
     * @param string $url Target redirect URL
     */
    public function redirect(string $url): void {
        header("Location: {$url}");
        exit;
    }

    /**
     * Send a JSON response payload
     * 
     * @param array $data Response payload
     * @param int $statusCode HTTP Status Code
     */
    public function json(array $data, int $statusCode = 200): void {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($statusCode);
        echo json_encode($data);
        exit;
    }
}
Class_Alias('App\\Core\\Response', 'Response');
