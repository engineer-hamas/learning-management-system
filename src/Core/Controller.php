<?php
namespace App\Core;

/**
 * Class Controller
 * 
 * Base Controller class that provides unified access to Request/Response methods.
 */
abstract class Controller {
    /**
     * Render an HTML view with extracted variable data
     * 
     * @param string $view View filename (without extension)
     * @param array $data Associative array of values
     */
    protected function render(string $view, array $data = [], ?string $layout = 'app'): void {
        (new Response())->render($view, $data, $layout ?? 'app');
    }

    /**
     * Perform HTTP redirection
     * 
     * @param string $url Redirect destination
     */
    protected function redirect(string $url): void {
        (new Response())->redirect($url);
    }

    /**
     * Send a JSON response payload
     * 
     * @param array $data Payload array
     * @param int $statusCode HTTP status code
     */
    protected function json(array $data, int $statusCode = 200): void {
        (new Response())->json($data, $statusCode);
    }
}
Class_Alias('App\\Core\\Controller', 'Controller');
