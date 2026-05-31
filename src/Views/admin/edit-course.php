<?php include __DIR__ . '/../components/common/page-header.php'; ?>

<div class="dashboard-container">
  <div class="glass-card animated-fade-in" style="max-width: 600px; margin: 0 auto;">
    <div style="margin-bottom: 2rem;">
      <h2>Edit Course</h2>
      <p style="color: var(--color-text-muted);">Update details for <?= e($course['name']) ?>.</p>
    </div>

    <?php if (!empty($error)): ?>
      <div class="alert alert-danger animated-shake" style="margin-bottom: 1.5rem;"><?= e($error) ?></div>
    <?php endif; ?>

    <form action="/admin/courses/edit" method="POST" class="styled-form">
      <input type="hidden" name="id" value="<?= $course['id'] ?>">

      <div class="form-group">
        <label for="department_id">Department</label>
        <select name="department_id" id="department_id" class="styled-select" required>
          <?php foreach ($departments as $dept): ?>
            <option value="<?= $dept['id'] ?>" <?= (int)$course['department_id'] === (int)$dept['id'] ? 'selected' : '' ?>><?= e($dept['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-group">
        <label for="name">Course Name</label>
        <input type="text" name="name" id="name" required value="<?= e($course['name']) ?>">
      </div>

      <div class="form-group">
        <label for="code">Course Code</label>
        <input type="text" name="code" id="code" required value="<?= e($course['code']) ?>">
      </div>

      <div class="form-group">
        <label for="credits">Credits</label>
        <input type="number" name="credits" id="credits" required min="1" max="6" value="<?= (int)$course['credits'] ?>">
      </div>

      <div class="form-group">
        <label for="description">Description</label>
        <textarea name="description" id="description" rows="4"><?= e($course['description'] ?? '') ?></textarea>
      </div>

      <div style="display: flex; gap: 1rem; margin-top: 2rem;">
        <button type="submit" class="btn-submit">Save Changes</button>
        <a href="/admin/courses" class="btn btn-secondary" style="flex: 1; text-align: center;">Cancel</a>
      </div>
    </form>
  </div>
</div>
