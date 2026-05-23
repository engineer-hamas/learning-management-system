<?php ob_start(); ?>
<div class="manage-enrollments-header">
    <h4 class="manage-title">Enroll Student</h4>
    <?php 
    // Prepare variables for search-enroll-form.php which expects $index, $allStudents, $subject
    $index = 'course_enroll'; 
    $subject = $course;
    require __DIR__ . '/../enrollments/search-enroll-form.php'; 
    ?>
</div>

<!-- Enrolled Students List -->
<?php if (empty($enrolledStudents)): ?>
    <div class="empty-state">
        <div class="empty-icon-lg">👥</div>
        No students are currently enrolled in this subject.
    </div>
<?php else: ?>
    <div class="enrolled-table-container">
        <table class="w-full-collapse">
            <thead>
                <tr class="table-head-row">
                    <th class="table-th-left">Student Name</th>
                    <th class="table-th-left">Email Address</th>
                    <th class="table-th-left">Enrolled On</th>
                    <th class="table-th-right">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($enrolledStudents as $stu): ?>
                    <tr class="table-body-row">
                        <td class="table-td-main">
                            <div class="flex-center-gap-12">
                                <div class="table-avatar-fallback">
                                    <?= strtoupper(substr(e($stu['name']), 0, 1)) ?>
                                </div>
                                <?= e($stu['name']) ?>
                            </div>
                        </td>
                        <td class="table-td-muted"><?= e($stu['email']) ?></td>
                        <td class="table-td-muted"><?= date('M j, Y \a\t g:i A', strtotime($stu['enrolled_at'])) ?></td>
                        <td class="table-td-right">
                            <form action="/enrollments/remove" method="POST" class="inline-form" onsubmit="return confirm('Unenroll student?');">
                                <input type="hidden" name="subject_id" value="<?= e($course['id']) ?>">
                                <input type="hidden" name="student_id" value="<?= e($stu['id']) ?>">
                                <button type="submit" class="btn-remove-enrollment" style="color: #ef4444; background: none; border: none; cursor: pointer; text-decoration: underline;">Remove</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
<?php 
$accordionContent = ob_get_clean(); 
$accordionId = 'enrollments-content';
$accordionTitle = '👥 Manage Enrollments';
$addAction = ''; // No Add button, form is inline
require __DIR__ . '/../common/accordion-section.php'; 
?>
