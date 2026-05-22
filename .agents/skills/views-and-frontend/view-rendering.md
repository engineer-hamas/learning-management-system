# View Rendering Subskill

This subskill outlines the standard structure of views and templates. Views are plain PHP files placed in the `src/Views/` directory, containing HTML structures mixed with simple PHP display constructs.

## View Scope Execution

Views are loaded inside the environment of `App\Core\Response` by calling `$response->render($viewName, $data)`. 

### The Extraction Mechanism
The `render()` method extracts the keys of the `$data` associative array into native variables using PHP's `extract()` function:

```php
// Inside Controller:
$this->render('dashboard', [
    'userName' => 'Sarah Connor',
    'userRole' => 'teacher'
]);

// Inside Response Class:
public function render(string $view, array $data = []): void {
    extract($data); // Converts 'userName' key to $userName variable!
    require_once dirname(__DIR__) . "/Views/{$view}.php";
}
```

## Structure of a Standard View

Always build HTML5-compliant layouts. Ensure semantic HTML structure, descriptive heading hierarchies, meta headers, and unique interactive IDs:

```php
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My View Title - LMS Portal</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">
</head>
<body class="dashboard-body">
    <div class="container">
        <h1>Welcome, <?= e($userName) ?></h1>
        <p>Your current role is: <strong><?= e($userRole) ?></strong></p>
    </div>
</body>
</html>
```

## Modular View Includes

If a view requires headers or footers repeatedly, separate them into `src/Views/partials/` and load them via standard `require` inside the view file:

```php
<?php require __DIR__ . '/partials/header.php'; ?>
<!-- Content here -->
<?php require __DIR__ . '/partials/footer.php'; ?>
```
