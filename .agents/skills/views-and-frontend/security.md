# Frontend Security (XSS Escaping) Subskill

This subskill outlines security requirements to prevent Cross-Site Scripting (XSS) injection. All dynamic content retrieved from the database or query strings must be properly escaped before outputting to HTML.

---

## Output Escaping Standard

Whenever outputting dynamic user records, roles, inputs, or database content inside HTML, **always wrap the output in the secure helper function `e()`**.

### Correct Escaping Syntax

Use the short echo tag `<?=` and the global `e()` escaping wrapper:

```html
<!-- Correct Outputting -->
<span class="profile-name"><?= e($userName) ?></span>

<p>User Role: <strong><?= e($userRole) ?></strong></p>
```

---

## Specification of the `e()` Helper

The escaping function is declared globally in `includes/functions.php` and loaded in the front controller. It sanitizes input by converting special HTML characters to secure HTML entities:

```php
/**
 * Escapes HTML characters for output safely
 *
 * @param string|null $string The string to escape
 * @return string The escaped string
 */
function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}
```

### Prohibited Code Patterns

- **NEVER** use standard `echo $variable` directly for user-controlled strings.
- **NEVER** output database values directly into javascript values without strict encoding.

```html
<!-- INCORRECT: HIGH XSS VULNERABILITY -->
<div><?= $user_input ?></div>

<!-- CORRECT: SECURE XSS PROTECTION -->
<div><?= e($user_input) ?></div>
```
