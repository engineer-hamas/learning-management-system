<?php ob_start(); ?>
<div><a href="/course?id=<?= e($course['id']) ?>" class="back-link">&larr; Back to <?= e($course['name']) ?></a></div>
<?php $headerTitle = ob_get_clean(); ?>
<?php require __DIR__ . '/components/common/page-header.php'; ?>

<div class="dashboard-container">
  <?php
  $bannerExtraClass = 'attendance-header-banner';
  $bannerTitleClass = 'attendance-title-lg text-title';
  $bannerTitle = '📊 Attendance Register';
  $bannerSubtitleClass = 'attendance-subtitle text-subtitle text-muted';
  $bannerSubtitle = '<strong>' . e($course['name']) . '</strong> (' . e($course['code']) . ')';
  $hideDescription = true;
  require __DIR__ . '/components/common/course-banner.php';
  ?>

  <!-- Universal Course Navigation Tabs -->
  <div class="nav-tabs" style="margin-top: 20px;">
    <a href="/course?id=<?= e($course['id']) ?>" class="nav-tabs__item">📚 Course Content</a>
    <a href="/course/attendance?id=<?= e($course['id']) ?>" class="nav-tabs__item nav-tabs__item--active">📊 Attendance</a>
    <?php if ($userRole === 'teacher'): ?>
      <a href="/course/manage-enrollments?id=<?= e($course['id']) ?>" class="nav-tabs__item">👥 Manage Enrollments</a>
    <?php endif; ?>
  </div>

  <?php if ($userRole === 'teacher'): ?>
    <div style="display: flex; justify-content: flex-end; margin-bottom: 20px;">
        <a href="/course/attendance/take?id=<?= e($course['id']) ?>" class="btn btn--primary">📝 Take Attendance</a>
    </div>
  <?php endif; ?>

  <?php if ($userRole === 'teacher'): ?>
    <?php if (empty($attendance_dates)): ?>
      <div class="empty-content empty-state">No attendance has been recorded for this course yet.</div>
    <?php else: ?>
      <?php require __DIR__ . '/components/attendance/teacher-matrix-table.php'; ?>
    <?php endif; ?>

  <?php else: ?>
    <?php require __DIR__ . '/components/attendance/student-summary-cards.php'; ?>

<?php if (empty($attendance_log)): ?>
  <div class="empty-content empty-state">No attendance records found yet.</div>
<?php else: ?>
  <table class="matrix-table" style="max-width: 800px; margin: 0 auto;">
    <thead>
      <tr>
        <th style="text-align: left;">Date</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($attendance_log as $log): ?>
        <tr>
          <td style="text-align: left; font-weight: 500; color: #1e293b;"><?= date('l, F j, Y', strtotime($log['date'])) ?></td>
          <td>
            <?php if ($log['status'] === 'present'): ?>
              <span class="status-badge status-present">Present</span>
            <?php elseif ($log['status'] === 'late'): ?>
              <span class="status-badge status-late">Late</span>
            <?php else: ?>
              <span class="status-badge status-absent">Absent</span>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
<?php endif; ?>
<?php endif; ?>

</div>
