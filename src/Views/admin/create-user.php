<?php include __DIR__ . '/../components/common/page-header.php'; ?>

<div class="glass-card animated-fade-in" style="max-width: 600px; margin: 0 auto;">
  <div style="margin-bottom: 2rem;">
    <h2>Create New User</h2>
    <p>Add a new teacher or student to the system.</p>
  </div>

  <?php if (!empty($error)): ?>
    <div class="alert alert-danger animated-shake"><?= e($error) ?></div>
  <?php endif; ?>

  <?php if (!empty($success)): ?>
    <div class="alert alert-success"><?= e($success) ?></div>
  <?php endif; ?>

  <form action="/admin/users/create" method="POST" class="styled-form">
    <div class="form-group">
      <label for="name">Full Name</label>
      <input type="text" name="name" id="name" required placeholder="John Doe">
    </div>

    <div class="form-group">
      <label for="email">Email Address</label>
      <input type="email" name="email" id="email" required placeholder="john@example.com">
    </div>

    <div class="form-group">
      <label for="password">Password</label>
      <input type="password" name="password" id="password" required placeholder="Temporary password">
    </div>

    <div class="form-group">
      <label for="role">User Role</label>
      <select name="role" id="role" class="styled-select" required>
        <option value="student">Student</option>
        <option value="teacher">Teacher</option>
      </select>
    </div>

    <div style="display: flex; gap: 1rem; margin-top: 2rem;">
      <button type="submit" class="btn-submit">Create User</button>
      <a href="/admin" class="btn btn-secondary" style="flex: 1; text-align: center;">Cancel</a>
    </div>
  </form>
</div>
