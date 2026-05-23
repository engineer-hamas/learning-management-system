<?php ob_start(); ?>
<h2><a href="/course?id=<?= e($course['id']) ?>" class="back-link">&larr; Back to <?= e($course['name']) ?></a></h2>
<?php $headerTitle = ob_get_clean(); ?>
<?php require __DIR__ . '/components/common/page-header.php'; ?>

<div class="dashboard-container">
  <?php
  $bannerExtraClass = 'attendance-header-banner';
  $bannerTitleClass = 'attendance-title-lg';
  $bannerTitleStyle = '';
  $bannerTitle = '📊 Attendance Register';
  $bannerSubtitleClass = 'attendance-subtitle';
  $bannerSubtitleStyle = '';
  $bannerSubtitle = '<strong>' . e($course['name']) . '</strong> (' . e($course['code']) . ')';
  $hideDescription = true;
  require __DIR__ . '/components/common/course-banner.php';
  ?>

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
