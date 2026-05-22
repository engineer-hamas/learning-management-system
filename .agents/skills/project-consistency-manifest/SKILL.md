---
name: Project Consistency Manifest
description: Establishes naming conventions, casing rules, structure, and placements to maintain 100% architectural purity in the LMS codebase.
---

# Project Consistency Manifest

This skill defines the rules for naming, directory placement, casing, and style conventions that MUST be followed by all developers and agents.

## Directory & File Placement Rules

All new files must be placed strictly according to their responsibility. Never create files outside these designated paths:

| File Type | Path Pattern | Examples |
| :--- | :--- | :--- |
| **Autoloadable Classes** | `src/{NamespaceDir}/{ClassName}.php` | `src/Core/Router.php`, `src/Controllers/AuthController.php` |
| **HTML/PHP Views** | `src/Views/{view-name}.php` | `src/Views/dashboard.php`, `src/Views/404.php` |
| **Style Source** | `src/scss/{style-name}.scss` | `src/scss/style.scss` |
| **Static Web Assets** | `public/{css/js/images}/{asset-name}.{ext}` | `public/css/style.css` |
| **Public Entrypoint** | `public/index.php` | Front Controller |
| **Global Config** | `config/config.php` | Settings, Session start |

## Casing and Naming Conventions

Maintain strict casing consistency across different layers of the application:

### 1. PHP Namespaces and Classes
- **Casing**: `PascalCase`
- **Namespaces**: Match the folder structure under `src/` prefixed by `App\`.
- **Suffixes**: 
  - Controllers must be suffixed with `Controller` (e.g., `AuthController`, `DashboardController`).
  - Core files must have clear responsibility names (e.g., `Router`, `Autoloader`, `Request`, `Response`).
  - Models must represent singular nouns (e.g., `User`).

*Correct Class Example:*
```php
namespace App\Controllers;

use App\Core\Controller;

class AuthController extends Controller {
    // ...
}
```

### 2. Methods and Functions
- **Casing**: `camelCase`
- **Prefixes**: Action-driven names (e.g., `handleLogin`, `showRegister`, `dispatch`, `bindParams`).

*Correct Method Example:*
```php
public function handleRegistrationSubmit() {
    // ...
}
```

### 3. Database Names and Columns
- **Tables**: Plural nouns in `snake_case` (e.g., `users`).
- **Columns**: `snake_case` (e.g., `id`, `name`, `email`, `created_at`).

*Correct SQL Example:*
```sql
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### 4. HTML/CSS and URLs
- **CSS classes**: `kebab-case` (e.g., `glass-card`, `btn-submit`, `animated-shake`).
- **URL routes**: `kebab-case` (e.g., `/dashboard`, `/register`, `/logout`).
- **HTML IDs**: `kebab-case` (e.g., `email-input`, `login-form`).

*Correct View Example:*
```html
<form id="login-form" action="/login" method="POST" class="styled-form">
    <div class="form-group">
        <label for="email-input">Email Address</label>
        <input type="email" id="email-input" class="form-input">
    </div>
</form>
```
