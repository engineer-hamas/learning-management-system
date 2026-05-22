# Prepared Statements Subskill

This subskill outlines our security standards and coding practices for database queries. **Direct concatenation or interpolation of variables in SQL strings is strictly banned** to prevent SQL injection vulnerabilities.

## SQL Statement Principles

- **Prepared Queries**: All SELECT, INSERT, UPDATE, and DELETE queries that incorporate user inputs must use MySQLi parameterized prepared statements.
- **Resources Lifecycle**: Always close queries (`$stmt->close()`) once execution is done to free up server and database resources.

---

## Code Reference Patterns

### 1. SELECT query (Checking user existence)

To evaluate if a record exists or count rows, use `$stmt->store_result()`:

```php
// 1. Prepare statement
$stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
if (!$stmt) {
    die("Query preparation failed: " . $this->db->error);
}

// 2. Bind parameter (s = string, i = integer, d = double, b = blob)
$stmt->bind_param("s", $email);

// 3. Execute
$stmt->execute();

// 4. Store result to obtain metadata
$stmt->store_result();

if ($stmt->num_rows > 0) {
    // Record exists!
}

// 5. Close statement resource
$stmt->close();
```

### 2. SELECT query (Fetching record data)

To fetch record associative arrays, use `$stmt->get_result()`:

```php
$stmt = $this->db->prepare("SELECT id, name, email, password, role FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();

// Retrieve result set
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    // Do business verification (e.g., password_verify)...
}
$stmt->close();
```

### 3. INSERT query (Inserting new user)

Execute insertions with strict types bindings:

```php
$stmt = $this->db->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
if (!$stmt) {
    die("Query preparation failed: " . $this->db->error);
}

// Bind multiple string parameters
$stmt->bind_param("ssss", $name, $email, $hashedPassword, $role);

if ($stmt->execute()) {
    // Record successfully inserted!
} else {
    // Execution failed
}

$stmt->close();
```
