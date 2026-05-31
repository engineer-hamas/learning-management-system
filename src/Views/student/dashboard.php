<?php include __DIR__ . '/../components/common/page-header.php'; ?>

<div class="dashboard-container">
  <div class="welcome-card animated-fade-in glass-card" style="margin-bottom: 2rem;">
    <h1>Welcome, <?= e($userName) ?>!</h1>
    <p style="color: #64748b; margin-top: 5px; font-size: 1.05rem;">
      Here's what's happening with your courses today.
    </p>
  </div>

  <div class="summary-cards-grid">
    <div class="summary-card-secondary animated-fade-in" style="animation-delay: 0.1s">
      <div class="stat-icon" style="font-size: 2rem; margin-bottom: 10px;">🎓</div>
      <div class="summary-card-label-secondary">Enrolled Courses</div>
      <div class="summary-card-value-secondary"><?= $totalEnrolledCourses ?? 0 ?></div>
    </div>
    
    <div class="summary-card-primary animated-fade-in" style="animation-delay: 0.2s">
      <div class="stat-icon" style="font-size: 2rem; margin-bottom: 10px;">📊</div>
      <div class="summary-card-label" style="color: rgba(255,255,255,0.9);">Latest Activity</div>
      <div class="summary-card-value"><?= count($recentMaterials ?? []) ?></div>
    </div>
  </div>

  <div class="admin-actions animated-fade-in glass-card" style="margin-top: 2rem; animation-delay: 0.3s;">
    <div style="margin-bottom: 1.5rem;">
      <h2>Recent Course Materials</h2>
      <p style="color: var(--color-text-muted); font-size: 0.95rem;">The latest files and resources shared by your teachers.</p>
    </div>
    
    <?php if (!empty($recentMaterials)): ?>
      <div class="enrolled-table-container">
        <table class="w-full-collapse">
          <thead>
            <tr class="table-head-row">
              <th class="table-th-left">Material Title</th>
              <th class="table-th-left">Course</th>
              <th class="table-th-right">Date Posted</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($recentMaterials as $mat): ?>
              <tr class="table-body-row">
                <td class="table-td-main">
                  <div class="flex-center-gap-12">
                    <span style="font-size: 1.2rem;">📄</span>
                    <?php if (!empty($mat['file_path'])): ?>
                        <a href="<?= e($mat['file_path']) ?>" target="_blank" style="color: #2563eb; text-decoration: none; font-weight: 600;"><?= e($mat['title']) ?></a>
                    <?php else: ?>
                        <?= e($mat['title']) ?>
                    <?php endif; ?>
                  </div>
                </td>
                <td class="table-td-muted"><?= e($mat['course_name']) ?></td>
                <td class="table-td-right" style="color: #64748b; font-size: 0.9rem;">
                    <?= date('M j, Y', strtotime($mat['created_at'])) ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <div class="empty-enrolled-box">
        <div class="empty-icon-lg">📂</div>
        <p>No recent materials have been posted in your enrolled courses.</p>
      </div>
    <?php endif; ?>
  </div>
</div>
