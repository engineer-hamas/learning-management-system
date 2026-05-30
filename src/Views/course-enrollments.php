<?php ob_start(); ?>
<div><a href="/enrollments" class="back-link">&larr; Back to Enrollments</a></div>
<?php $headerTitle = ob_get_clean(); ?>
<?php require __DIR__ . '/components/common/page-header.php'; ?>

<div class="dashboard-container">
  <?php
  $bannerExtraClass = 'course-header-banner';
  $bannerTitleClass = 'text-title';
  $bannerTitle = '👥 Manage Enrollments';
  $bannerSubtitleClass = 'text-subtitle text-muted';
  $bannerSubtitle = '<strong>' . e($course['name']) . '</strong> (' . e($course['code']) . ')';
  $hideDescription = true;
  require __DIR__ . '/components/common/course-banner.php';
  ?>

  <!-- Universal Course Navigation Tabs -->
  <div class="nav-tabs" style="margin-top: 20px;">
    <a href="/course?id=<?= e($course['id']) ?>" class="nav-tabs__item">📚 Course Content</a>
    <a href="/course/attendance?id=<?= e($course['id']) ?>" class="nav-tabs__item">📊 Attendance</a>
    <?php if ($userRole === 'teacher'): ?>
      <a href="/course/manage-enrollments?id=<?= e($course['id']) ?>" class="nav-tabs__item nav-tabs__item--active">👥 Manage Enrollments</a>
    <?php endif; ?>
  </div>

  <div class="card card--glass" style="margin-top: 20px;">
    <div class="section-header">
        <h3>Enroll Student</h3>
        <?php 
        // Prepare variables for search-enroll-form.php which expects $index, $allStudents, $subject
        $index = 'course_enroll'; 
        $subject = $course;
        $subject['enrolled'] = $enrolledStudents;
        require __DIR__ . '/components/enrollments/search-enroll-form.php'; 
        ?>
    </div>

    <!-- Enrolled Students List -->
    <?php if (empty($enrolledStudents)): ?>
        <div class="empty-state card--dashed" style="padding: 40px; text-align: center;">
            <div style="font-size: 3rem; margin-bottom: 10px;">👥</div>
            <p class="text-subtitle text-muted">No students are currently enrolled in this subject.</p>
        </div>
    <?php else: ?>
        <div style="overflow-x: auto; margin-top: 20px;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 2px solid var(--color-border);">
                        <th style="text-align: left; padding: 12px; color: var(--color-text-muted);">Student Name</th>
                        <th style="text-align: left; padding: 12px; color: var(--color-text-muted);">Email Address</th>
                        <th style="text-align: left; padding: 12px; color: var(--color-text-muted);">Enrolled On</th>
                        <th style="text-align: right; padding: 12px; color: var(--color-text-muted);">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($enrolledStudents as $stu): ?>
                        <tr style="border-bottom: 1px solid var(--color-border);">
                            <td style="padding: 15px 12px;">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--color-primary); color: white; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 0.85rem;">
                                        <?= strtoupper(substr(e($stu['name']), 0, 1)) ?>
                                    </div>
                                    <span class="text-body font-medium"><?= e($stu['name']) ?></span>
                                </div>
                            </td>
                            <td style="padding: 15px 12px;" class="text-muted"><?= e($stu['email']) ?></td>
                            <td style="padding: 15px 12px;" class="text-muted"><?= date('M j, Y', strtotime($stu['enrolled_at'])) ?></td>
                            <td style="padding: 15px 12px; text-align: right;">
                                <form action="/enrollments/remove" method="POST" style="margin: 0; display: inline-block;" onsubmit="return confirm('Unenroll student?');">
                                    <input type="hidden" name="subject_id" value="<?= e($course['id']) ?>">
                                    <input type="hidden" name="student_id" value="<?= e($stu['id']) ?>">
                                    <button type="submit" class="btn btn--destructive">Remove</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
  </div>
</div>

<script>
// Searchable Dropdown Logic
function openDropdown(index) {
    document.getElementById('dropdown-list-' + index).style.display = 'block';
    document.getElementById('search-input-' + index).style.borderColor = 'var(--color-primary)';
}

function filterDropdown(index) {
    let input = document.getElementById('search-input-' + index).value.toLowerCase();
    let items = document.querySelectorAll('#dropdown-list-' + index + ' .dropdown-item');
    let hasResults = false;
    
    items.forEach(item => {
        if (item.getAttribute('data-search').includes(input)) {
            item.style.display = 'flex';
            hasResults = true;
        } else {
            item.style.display = 'none';
        }
    });
    
    document.getElementById('no-results-' + index).style.display = hasResults ? 'none' : 'block';
}

function selectOption(index, id, text) {
    document.getElementById('hidden-input-' + index).value = id;
    document.getElementById('search-input-' + index).value = text;
    document.getElementById('dropdown-list-' + index).style.display = 'none';
    document.getElementById('search-input-' + index).style.borderColor = 'var(--color-border)';
}

// Close dropdown when clicking outside
document.addEventListener('click', function(e) {
    let dropdowns = document.querySelectorAll('.searchable-dropdown');
    dropdowns.forEach(dropdown => {
        if (!dropdown.contains(e.target)) {
            let list = dropdown.querySelector('.dropdown-list-container');
            if (list) list.style.display = 'none';
            let input = dropdown.querySelector('.searchable-input');
            if (input) input.style.borderColor = 'var(--color-border)';
        }
    });
});
</script>
