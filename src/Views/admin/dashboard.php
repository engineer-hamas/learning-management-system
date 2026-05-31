<?php include __DIR__ . '/../components/common/page-header.php'; ?>

<div class="summary-cards-grid">
  <!-- Stats Cards -->
  <div class="summary-card-secondary animated-fade-in" style="animation-delay: 0.1s">
    <div class="stat-icon" style="font-size: 2rem; margin-bottom: 10px;">👨‍🏫</div>
    <div class="summary-card-label-secondary">Total Teachers</div>
    <div class="summary-card-value-secondary"><?= $teachersCount ?></div>
  </div>

  <div class="summary-card-secondary animated-fade-in" style="animation-delay: 0.2s">
    <div class="stat-icon" style="font-size: 2rem; margin-bottom: 10px;">👨‍🎓</div>
    <div class="summary-card-label-secondary">Total Students</div>
    <div class="summary-card-value-secondary"><?= $studentsCount ?></div>
  </div>

  <div class="summary-card-primary animated-fade-in" style="animation-delay: 0.3s">
    <div class="stat-icon" style="font-size: 2rem; margin-bottom: 10px;">📚</div>
    <div class="summary-card-label" style="color: rgba(255,255,255,0.9);">Total Courses</div>
    <div class="summary-card-value"><?= $coursesCount ?></div>
  </div>
</div>

<div class="admin-actions animated-fade-in glass-card" style="margin-top: 2rem;">
  <div style="margin-bottom: 1.5rem;">
    <h2>Quick Actions</h2>
    <p style="color: var(--color-text-muted); font-size: 0.95rem;">Manage your platform's users and curriculum.</p>
  </div>
  <div style="display: flex; gap: 1rem;">
    <a href="/admin/users/create" class="btn-enroll-submit" style="text-decoration: none; padding: 12px 24px;">➕ Create User</a>
    <a href="/admin/courses/create" class="btn-remove-enrollment" style="text-decoration: none; padding: 12px 24px; border-color: #cbd5e1; background: #f8fafc; color: #334155;">➕ Create Course</a>
  </div>
</div>
