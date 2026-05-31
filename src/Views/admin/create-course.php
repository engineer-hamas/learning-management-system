<?php include __DIR__ . '/../components/common/page-header.php'; ?>

<div class="glass-card animated-fade-in" style="max-width: 600px; margin: 0 auto;">
  <div style="margin-bottom: 2rem;">
    <h2>Create New Course</h2>
    <p>Add a new subject to the curriculum.</p>
  </div>

  <?php if (!empty($error)): ?>
    <div class="alert alert-danger animated-shake"><?= e($error) ?></div>
  <?php endif; ?>

  <?php if (!empty($success)): ?>
    <div class="alert alert-success"><?= e($success) ?></div>
  <?php endif; ?>

  <form action="/admin/courses/create" method="POST" class="styled-form">
    <div class="form-group">
      <label for="department_id">Department</label>
      <select name="department_id" id="department_id" class="styled-select" required>
        <?php foreach ($departments as $dept): ?>
          <option value="<?= $dept['id'] ?>"><?= e($dept['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="form-group">
      <label for="name">Course Name</label>
      <input type="text" name="name" id="name" required placeholder="Introduction to Advanced PHP">
    </div>

    <div class="form-group">
      <label for="code">Course Code</label>
      <input type="text" name="code" id="code" required placeholder="CS505">
    </div>

    <div class="form-group">
      <label for="credits">Credits</label>
      <input type="number" name="credits" id="credits" required min="1" max="6" value="3">
    </div>

    <div class="form-group">
      <label for="description">Description</label>
      <textarea name="description" id="description" rows="4" placeholder="Course description..."></textarea>
    </div>

    <div style="display: flex; gap: 1rem; margin-top: 2rem;">
      <button type="submit" class="btn-submit">Create Course</button>
      <a href="/admin" class="btn btn-secondary" style="flex: 1; text-align: center;">Cancel</a>
    </div>
  </form>
</div>
