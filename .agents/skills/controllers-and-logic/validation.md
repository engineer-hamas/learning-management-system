# Input Validation Subskill

This subskill governs safe collection, trimming, format validation, and logical constraints verification for all inputs submitted by users.

## Input Processing Flow

Always validate input at the controller level immediately upon request arrival:

```
[ Request Arrival ] ➔ [ Trim Inputs ] ➔ [ Presence Check ] ➔ [ Format/Type Verification ] ➔ [ Save Error / Redirect ]
```

## Form Input Validation Standard

### 1. Unified Sanitization and Trimming
Always clean incoming strings to remove accidental leading or trailing whitespaces. Use the `cleanPost()` method on the `Request` object:

```php
$email = $request->cleanPost('email');
$name = $request->cleanPost('name');
```

### 2. Presence Verification
Verify that required fields are not blank or empty.

```php
if (empty($name) || empty($email) || empty($password)) {
    $_SESSION['register_error'] = "All fields are required.";
    $response->redirect('/register');
}
```

### 3. Type and Format Verification
Use native PHP functions for secure format checks:
- **Emails**: `filter_var($email, FILTER_VALIDATE_EMAIL)`
- **Enum values**: `in_array($role, ['student', 'teacher'])`

```php
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['register_error'] = "Invalid email format.";
    $response->redirect('/register');
}
```

## Session-Based Error Reporting

Since we use standard HTTP redirects to avoid form re-submission on page refreshes, you must store error and success strings in `$_SESSION` temporary keys, and clear them inside the view rendering controller actions:

```php
// Inside Submission Handler Controller Action:
$_SESSION['register_error'] = "Email is already registered.";
$response->redirect('/register');

// Inside View Rendering Controller Action:
$error = $_SESSION['register_error'] ?? '';
unset($_SESSION['register_error']); // Clear after reading
$this->render('register', ['error' => $error]);
```
