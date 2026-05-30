<?php ob_start(); ?>
<h2>Course Enrollments</h2>
<?php $headerTitle = ob_get_clean(); ?>
<?php require __DIR__ . '/components/common/page-header.php'; ?>

<div class="dashboard-container">
  <?php if (!empty($error)): ?>
    <div class="alert alert-danger" class="alert alert--danger">
      <?= e($error) ?>
    </div>
  <?php endif; ?>

  <?php if (!empty($success)): ?>
    <div class="alert alert-success" class="alert alert--success">
      <?= e($success) ?>
    </div>
  <?php endif; ?>

  <?php if ($userRole === 'teacher'): ?>
    <!-- Teacher View -->
    <div class="card card--glass mb-30">
      <h1 class="text-title">Manage Enrollments</h1>
      <p class="text-body">Below are the subjects you teach. You can enroll or remove students from your courses.</p>
    </div>

    <?php if (empty($teacherSubjects)): ?>
      <div class="empty-state" class="empty-state">
        <p>You are not currently assigned to teach any subjects.</p>
        <p class="text-muted-sm">Update your subjects in your Professional Settings.</p>
      </div>
    <?php else: ?>
      <div class="student-courses-grid">
        <?php foreach ($teacherSubjects as $index => $sub): ?>
          <a href="/course?id=<?= e($sub['id']) ?>" class="course-link-block">
            <div class="card card--glass" style="display:flex; flex-direction:column; justify-content:space-between; height:100%;">
              <div>
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
                  <div style="font-size:2rem;">📚</div>
                  <span style="background:var(--color-bg); padding:4px 8px; border-radius:4px; font-size:0.85rem; font-weight:600; color:var(--color-text-muted);"><?= e($sub['code']) ?></span>
                </div>
                <h3 class="text-subtitle" style="margin-bottom:10px;"><?= e($sub['name']) ?></h3>
                <p class="text-muted" style="margin-bottom:20px;">
                  <span>👥</span> <?= count($sub['enrolled']) ?> Student(s) Enrolled
                </p>
              </div>
              <div class="btn btn--primary" style="width:100%; justify-content:center;">
                Manage Course &rarr;
              </div>
            </div>
          </a>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

  <?php else: ?>
    <!-- Student View -->
    <div class="card card--glass mb-30">
      <h1 class="text-title">My Enrollments</h1>
      <p class="text-body">Below are the subjects you are currently enrolled in. If you need to drop a course, please contact your instructor.</p>
    </div>

    <?php if (empty($studentEnrollments)): ?>
      <div class="empty-state" class="empty-state">
        <p>You are not currently enrolled in any subjects.</p>
      </div>
    <?php else: ?>
      <div class="student-courses-grid">
        <?php foreach ($studentEnrollments as $sub): ?>
          <a href="/course?id=<?= e($sub['id']) ?>" class="course-link-block">
            <div class="card card--glass" style="display:flex; flex-direction:column; justify-content:space-between; height:100%;">
              <div>
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
                  <div style="font-size:2rem;">📘</div>
                  <span style="background:var(--color-bg); padding:4px 8px; border-radius:4px; font-size:0.85rem; font-weight:600; color:var(--color-text-muted);"><?= e($sub['code']) ?></span>
                </div>
                <h3 class="text-subtitle" style="margin-bottom:10px;"><?= e($sub['name']) ?></h3>
                <p class="text-muted" style="margin-bottom:20px;">
                  <span>🗓️</span> Enrolled <?= date('M j, Y', strtotime($sub['enrolled_at'])) ?>
                </p>
              </div>
              <div class="btn btn--primary" style="width:100%; justify-content:center;">
                Enter Course &rarr;
              </div>
            </div>
          </a>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  <?php endif; ?>
</div>
