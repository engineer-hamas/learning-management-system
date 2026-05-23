# View Rendering Subskill

This subskill outlines the standard structure of views and templates. Views are plain PHP files placed in the `src/Views/` directory, containing HTML structures mixed with simple PHP display constructs.

## View Scope Execution

Views are loaded inside the environment of `App\Core\Response` by calling `$response->render($viewName, $data, $layout)`. 

### The Extraction Mechanism
The `render()` method extracts the keys of the `$data` associative array into native variables using PHP's `extract()` function:

```php
// Inside Controller:
$this->render('dashboard', [
    'userName' => 'Sarah Connor',
    'userRole' => 'teacher'
]);

// Inside Response Class:
public function render(string $view, array $data = [], string $layout = 'app'): void {
    extract($data); // Converts 'userName' key to $userName variable!
    
    // Uses Output Buffering to capture the view
    // and injects it as $content into the layout wrapper.
}
```

## DRY Principle & Layout Components

Always build DRY (Don't Repeat Yourself) interfaces. Avoid duplicating the `<!DOCTYPE html>`, `<head>`, or structural `<aside>` sidebars across multiple views.

### 1. The Layout File
Layouts are stored in `src/Views/layouts/`. The default layout is `app.php`, which wraps the authenticated user experience. It contains the boilerplate HTML and echoes the view content where appropriate using `<?= $content ?>`.

Another layout is `auth.php` (used for login and registration).

### 2. Structure of a Standard View
Because of the layout wrapper, a standard view file (e.g. `src/Views/dashboard.php`) should **ONLY** contain the specific HTML content required for that page. Do NOT include `<html>`, `<body>`, or the sidebar in the view file:

```php
<!-- src/Views/dashboard.php -->
<header class="dashboard-header">
    <div class="header-title">
        <h2>Dashboard Overview</h2>
    </div>
</header>
<div class="dashboard-container">
    <h1>Welcome, <?= e($userName) ?></h1>
    <p>Your current role is: <strong><?= e($userRole) ?></strong></p>
</div>
```

## Specifying a Different Layout

If a view requires a different layout (such as the authentication wrapper without a sidebar), specify the layout name as the third parameter in the controller:

```php
$this->render('login', ['error' => $error], 'auth');
```

To render a view without any layout, pass `null` or an empty string as the layout parameter:

```php
$this->render('ajax_partial', $data, null);
```

### 3. Sub-Components (Modular Partials)
Large views should be broken down into modular components within the `src/Views/components/` directory:
- Shared components (like page headers, alerts, course banners) are stored in `components/common/`.
- Domain-specific components are stored in directories like `components/enrollments/` or `components/attendance/`.

Components are included via the `require` statement, which inherits the parent view's variable scope:

```php
<?php 
$headerTitle = '<h2>Course Enrollments</h2>';
require __DIR__ . '/components/common/page-header.php'; 
?>
```
