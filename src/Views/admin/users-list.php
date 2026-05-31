<?php include __DIR__ . '/../components/common/page-header.php'; ?>

<div class="dashboard-container">
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <div>
      <h2>Manage Users</h2>
      <p style="color: var(--color-text-muted);">View, edit, and manage all teachers and students.</p>
    </div>
    <a href="/admin/users/create" class="btn-enroll" style="text-decoration: none; padding: 10px 20px;">➕ Add New User</a>
  </div>

  <div style="margin-bottom: 1.5rem;">
    <input type="text" id="user-search" class="styled-input" placeholder="🔍 Search users by name or email..." style="width: 100%; max-width: 400px; padding: 12px; border-radius: 8px; border: 1px solid #e2e8f0;">
  </div>

  <?php if (!empty($error)): ?>
    <div class="alert alert-danger animated-shake" style="margin-bottom: 1.5rem;"><?= e($error) ?></div>
  <?php endif; ?>

  <?php if (!empty($success)): ?>
    <div class="alert alert-success" style="margin-bottom: 1.5rem;"><?= e($success) ?></div>
  <?php endif; ?>

  <div class="glass-card animated-fade-in">
    <?php if (!empty($users)): ?>
      <div class="enrolled-table-container">
        <table class="w-full-collapse">
          <thead>
            <tr class="table-head-row">
              <th class="table-th-left">Name</th>
              <th class="table-th-left">Email</th>
              <th class="table-th-left">Role</th>
              <th class="table-th-left">Joined</th>
              <th class="table-th-right">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($users as $user): ?>
              <tr class="table-body-row">
                <td class="table-td-main">
                  <strong><?= e($user['name']) ?></strong>
                </td>
                <td class="table-td-muted"><?= e($user['email']) ?></td>
                <td class="table-td-muted">
                    <?php if ($user['role'] === 'teacher'): ?>
                        <span style="background: #e0e7ff; color: #3730a3; padding: 4px 8px; border-radius: 4px; font-size: 0.85rem; font-weight: 600;">Teacher</span>
                    <?php else: ?>
                        <span style="background: #f1f5f9; color: #475569; padding: 4px 8px; border-radius: 4px; font-size: 0.85rem; font-weight: 600;">Student</span>
                    <?php endif; ?>
                </td>
                <td class="table-td-muted">
                    <?= date('M j, Y', strtotime($user['created_at'])) ?>
                </td>
                <td class="table-td-right">
                  <div style="display: flex; gap: 8px; justify-content: flex-end;">
                    <a href="/admin/users/edit?id=<?= $user['id'] ?>" class="btn-enroll" style="text-decoration: none; padding: 6px 12px; font-size: 0.9rem;">Edit</a>
                    
                    <form action="/admin/users/delete" method="POST" onsubmit="return confirm('Are you sure you want to delete this user? This will also permanently delete all their enrollments, grades, and course materials. This action cannot be undone.');">
                      <input type="hidden" name="id" value="<?= $user['id'] ?>">
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
        <div class="empty-icon-lg">👥</div>
        <p>No non-admin users found in the system.</p>
      </div>
    <?php endif; ?>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('user-search');
    if (searchInput) {
      searchInput.addEventListener('keyup', function() {
        const query = this.value.toLowerCase();
        const rows = document.querySelectorAll('.table-body-row');
        
        rows.forEach(row => {
          const name = row.querySelector('.table-td-main strong')?.textContent.toLowerCase() || '';
          const email = row.querySelector('.table-td-muted')?.textContent.toLowerCase() || '';
          
          if (name.includes(query) || email.includes(query)) {
            row.style.display = '';
          } else {
            row.style.display = 'none';
          }
        });
      });
    }
  });
</script>
