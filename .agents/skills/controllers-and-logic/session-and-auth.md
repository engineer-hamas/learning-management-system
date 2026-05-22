# Session and Authentication Subskill

This subskill outlines security policies and practices relating to authentication flows, password encryption, session storage, and route authorization.

## Authentication and Redirection Rules

- **Authenticated Session Lock**: Pages that require login (e.g. `/dashboard`) must verify that the session has `$_SESSION['user_id']`. If not present, they must immediately redirect to `/login`.
- **Guest Access Lock**: Pages meant for guests (e.g. `/login`, `/register`) must verify if the session has `$_SESSION['user_id']`. If present, they must immediately redirect to `/dashboard`.

*Example Router/Controller Lock:*
```php
if (!isset($_SESSION['user_id'])) {
    $response->redirect('/login');
}
```

## Session Management Policies

### 1. Prevent Session Fixation
Upon successful login validation, you MUST call `session_regenerate_id(true)` before storing user data. This invalidates the old session ID and generates a secure new one.

```php
// Verify credentials...
if (password_verify($password, $user['password'])) {
    // Regenerate ID
    session_regenerate_id(true);

    // Save profile metadata
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_role'] = $user['role'];
}
```

### 2. Standardized Login Session Payload
Store only primary user metadata in the session. Maintain a consistent naming structure:
- `$_SESSION['user_id']` (Integer ID)
- `$_SESSION['user_name']` (String Full Name)
- `$_SESSION['user_role']` (Enum: `'student'` or `'teacher'`)
- `$_SESSION['user_email']` (String Email)

### 3. Secure Logout Lifecycle
When logging out, clear the payload array, destroy the server-side session, and invalidate the browser's cookie to prevent reuse:

```php
public function handleLogout(Request $request, Response $response): void {
    $_SESSION = [];
    
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(), 
            '', 
            time() - 42000,
            $params["path"], 
            $params["domain"],
            $params["secure"], 
            $params["httponly"]
        );
    }
    
    session_destroy();
    $response->redirect('/login');
}
```
