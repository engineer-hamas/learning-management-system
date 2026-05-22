<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - LMS Portal</title>
    <!-- Premium Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">
</head>
<body class="auth-body">
    <div class="auth-wrapper">
        <div class="glass-card animated-fade-in">
            <div class="auth-header">
                <h2>Create Account</h2>
                <p>Join our modern LMS platform today</p>
            </div>

            <!-- Error Notifications -->
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger animated-shake">
                    <?= e($error) ?>
                </div>
            <?php endif; ?>

            <form action="/register" method="POST" class="styled-form">
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" name="name" id="name" placeholder="John Doe" required autocomplete="name">
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" name="email" id="email" placeholder="john@lms.com" required autocomplete="email">
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" placeholder="••••••••" required autocomplete="new-password">
                </div>

                <div class="form-group">
                    <label for="role">I am a...</label>
                    <select name="role" id="role" class="styled-select" required>
                        <option value="student">Student</option>
                        <option value="teacher">Teacher / Instructor</option>
                    </select>
                </div>

                <button type="submit" class="btn-submit">Sign Up</button>
            </form>

            <div class="auth-footer">
                <p>Already have an account? <a href="/login">Sign in instead</a></p>
            </div>
        </div>
    </div>
</body>
</html>
