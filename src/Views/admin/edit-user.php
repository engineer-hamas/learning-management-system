<?php include __DIR__ . '/../components/common/page-header.php'; ?>

<div class="dashboard-container">
  <div class="glass-card animated-fade-in" style="max-width: 600px; margin: 0 auto;">
    <div style="margin-bottom: 2rem;">
      <h2>Edit User</h2>
      <p style="color: var(--color-text-muted);">Update profile details for <?= e($user['name']) ?>.</p>
    </div>

    <?php if (!empty($error)): ?>
      <div class="alert alert-danger animated-shake" style="margin-bottom: 1.5rem;"><?= e($error) ?></div>
    <?php endif; ?>

    <form action="/admin/users/edit" method="POST" class="styled-form">
      <input type="hidden" name="id" value="<?= $user['id'] ?>">
      
      <div class="form-group">
        <label for="name">Full Name</label>
        <input type="text" name="name" id="name" required value="<?= e($user['name']) ?>">
      </div>

      <div class="form-group">
        <label for="email">Email Address</label>
        <input type="email" name="email" id="email" required value="<?= e($user['email']) ?>">
      </div>

      <div class="form-group">
        <label for="role">User Role</label>
        <select name="role" id="role" class="styled-select" required>
          <option value="student" <?= $user['role'] === 'student' ? 'selected' : '' ?>>Student</option>
          <option value="teacher" <?= $user['role'] === 'teacher' ? 'selected' : '' ?>>Teacher</option>
        </select>
        <small style="color: #64748b; margin-top: 5px; display: block;">Note: Changing a user's role will not automatically create or delete their role-specific profile (like office hours or graduation date).</small>
      </div>

      <div style="display: flex; gap: 1rem; margin-top: 2rem;">
        <button type="submit" class="btn-submit">Save Changes</button>
        <a href="/admin/users" class="btn btn-secondary" style="flex: 1; text-align: center;">Cancel</a>
      </div>
    </form>
  </div>
</div>
