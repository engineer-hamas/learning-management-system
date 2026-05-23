<div class="auth-wrapper">
  <div class="glass-card animated-fade-in">
    <div class="auth-header">
      <h2>Welcome </h2>
      <p>Log in to access your dashboard</p>
    </div>

    <!-- Error Notifications -->
    <?php if (!empty($error)): ?>
      <div class="alert alert-danger animated-shake">
        <?= e($error) ?>
      </div>
    <?php endif; ?>

    <!-- Success Notifications -->
    <?php if (!empty($success)): ?>
      <div class="alert alert-success">
        <?= e($success) ?>
      </div>
    <?php endif; ?>

    <form action="/login" method="POST" class="styled-form">
      <div class="form-group">
        <label for="email">Email Address</label>
        <input type="email" name="email" id="email" placeholder="name@lms.com" required autocomplete="email">
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" name="password" id="password" placeholder="••••••••" required autocomplete="current-password">
      </div>

      <button type="submit" class="btn-submit">Sign In</button>
    </form>

    <div class="auth-footer">
      <p>Don't have an account? <a href="/register">Create an account</a></p>
    </div>
  </div>
</div>
