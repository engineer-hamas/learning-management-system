<?php ob_start(); ?>
<h2><a href="/course?id=<?= e($course['id']) ?>" class="back-link">&larr; Back to <?= e($course['name']) ?></a></h2>
<?php $headerTitle = ob_get_clean(); ?>
<?php require __DIR__ . '/components/common/page-header.php'; ?>

<div class="dashboard-container">
  <?php
  $bannerExtraClass = 'attendance-header-banner';
  $bannerTitleClass = 'attendance-title-lg';
  $bannerTitleStyle = '';
  $bannerTitle = '📝 Take Attendance';
  $bannerSubtitleClass = 'attendance-subtitle';
  $bannerSubtitleStyle = '';
  $bannerSubtitle = '<strong>' . e($course['name']) . '</strong> (' . e($course['code']) . ')';
  $hideDescription = true;
  require __DIR__ . '/components/common/course-banner.php';
  ?>

  <form action="/course/attendance/mark" method="POST" class="attendance-form-container">
    <input type="hidden" name="subject_id" value="<?= e($course['id']) ?>">

    <div class="date-picker-container">
      <label class="date-picker-label">Select Date</label>
      <input type="date" name="attendance_date" class="form-control" value="<?= date('Y-m-d') ?>" class="date-picker-input" required>
    </div>

    <?php if (empty($enrolled_students)): ?>
      <div class="empty-content" class="empty-state-dashed">No students are currently enrolled in this course.</div>
    <?php else: ?>
      <div class="list-header-flex">
        <h3 class="list-header-title">Enrolled Students</h3>
        <button type="submit" class="btn-save-attendance">💾 Save Attendance</button>
      </div>

      <?php require __DIR__ . '/components/attendance/roster-radio-table.php'; ?>

      <div class="text-right">
        <button type="submit" class="btn-save-attendance-lg">💾 Save Attendance</button>
      </div>
    <?php endif; ?>
  </form>

</div>
