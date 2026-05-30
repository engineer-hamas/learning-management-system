<?php ob_start(); ?>
<div><a href="/course?id=<?= e($course['id']) ?>" class="back-link">&larr; Back to <?= e($course['name']) ?></a></div>
<?php $headerTitle = ob_get_clean(); ?>
<?php require __DIR__ . '/components/common/page-header.php'; ?>

<div class="dashboard-container">
  <?php
  $bannerExtraClass = 'attendance-header-banner';
  $bannerTitleClass = 'attendance-title-lg text-title';
  $bannerTitle = '📝 Take Attendance';
  $bannerSubtitleClass = 'attendance-subtitle text-subtitle text-muted';
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

  <div style="margin-bottom: 20px;">
      <a href="/course/attendance?id=<?= e($course['id']) ?>" class="btn btn--ghost">&larr; Back to Attendance Records</a>
  </div>

  <form action="/course/attendance/mark" method="POST" class="attendance-form-container">
    <input type="hidden" name="subject_id" value="<?= e($course['id']) ?>">

    <div class="date-picker-container">
      <label class="date-picker-label">Select Date</label>
      <input type="date" name="attendance_date" class="form-control date-picker-input" value="<?= date('Y-m-d') ?>" required>
    </div>

    <?php if (empty($enrolled_students)): ?>
      <div class="empty-state" class="empty-state">No students are currently enrolled in this course.</div>
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
