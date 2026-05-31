<?php include __DIR__ . '/../components/common/page-header.php'; ?>

<div class="dashboard-container">
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <div>
      <h2>Manage Courses</h2>
      <p style="color: var(--color-text-muted);">View, edit, and manage the platform curriculum.</p>
    </div>
    <a href="/admin/courses/create" class="btn-enroll" style="text-decoration: none; padding: 10px 20px;">➕ Add New Course</a>
  </div>

  <div style="margin-bottom: 1.5rem;">
    <input type="text" id="course-search" class="styled-input" placeholder="🔍 Search courses by name or code..." style="width: 100%; max-width: 400px; padding: 12px; border-radius: 8px; border: 1px solid #e2e8f0;">
  </div>

  <?php if (!empty($error)): ?>
    <div class="alert alert-danger animated-shake" style="margin-bottom: 1.5rem;"><?= e($error) ?></div>
  <?php endif; ?>

  <?php if (!empty($success)): ?>
    <div class="alert alert-success" style="margin-bottom: 1.5rem;"><?= e($success) ?></div>
  <?php endif; ?>

  <div class="glass-card animated-fade-in">
    <?php if (!empty($courses)): ?>
      <div class="enrolled-table-container">
        <table class="w-full-collapse">
          <thead>
            <tr class="table-head-row">
              <th class="table-th-left">Course Code</th>
              <th class="table-th-left">Course Name</th>
              <th class="table-th-left">Credits</th>
              <th class="table-th-right">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($courses as $course): ?>
              <tr class="table-body-row">
                <td class="table-td-muted">
                    <span style="background: #f1f5f9; color: #475569; padding: 4px 8px; border-radius: 4px; font-size: 0.85rem; font-weight: 600; font-family: monospace;"><?= e($course['code']) ?></span>
                </td>
                <td class="table-td-main">
                  <strong><?= e($course['name']) ?></strong>
                </td>
                <td class="table-td-muted">
                    <?= (int)$course['credits'] ?>
                </td>
                <td class="table-td-right">
                  <div style="display: flex; gap: 8px; justify-content: flex-end;">
                    <a href="/admin/courses/edit?id=<?= $course['id'] ?>" class="btn-enroll" style="text-decoration: none; padding: 6px 12px; font-size: 0.9rem;">Edit</a>
                    
                    <form action="/admin/courses/delete" method="POST" onsubmit="return confirm('Are you sure you want to delete this course? This will permanently delete all enrollments, assignments, and course materials tied to it. This action cannot be undone.');">
                      <input type="hidden" name="id" value="<?= $course['id'] ?>">
                      <button type="submit" class="btn-remove-enrollment" style="padding: 6px 12px; font-size: 0.9rem;">Delete</button>
                    </form>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <div class="empty-enrolled-box">
        <div class="empty-icon-lg">📚</div>
        <p>No courses found in the system.</p>
      </div>
    <?php endif; ?>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('course-search');
    if (searchInput) {
      searchInput.addEventListener('keyup', function() {
        const query = this.value.toLowerCase();
        const rows = document.querySelectorAll('.table-body-row');
        
        rows.forEach(row => {
          const code = row.querySelector('.table-td-muted span')?.textContent.toLowerCase() || '';
          const name = row.querySelector('.table-td-main strong')?.textContent.toLowerCase() || '';
          
          if (name.includes(query) || code.includes(query)) {
            row.style.display = '';
          } else {
            row.style.display = 'none';
          }
        });
      });
    }
  });
</script>
