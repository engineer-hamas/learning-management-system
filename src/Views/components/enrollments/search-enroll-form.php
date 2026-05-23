<form action="/enrollments/add" method="POST" class="enroll-form-flex">
  <input type="hidden" name="subject_id" value="<?= e($subject['id']) ?>">

  <div class="searchable-dropdown" class="relative-wrapper">
    <input type="text" id="search-input-<?= $index ?>" placeholder="Search student name or email..." class="searchable-input" onfocus="openDropdown(<?= $index ?>)" oninput="filterDropdown(<?= $index ?>)" autocomplete="off">
    <input type="hidden" name="student_id" id="hidden-input-<?= $index ?>" required>

    <div id="dropdown-list-<?= $index ?>" class="dropdown-list-container" style="display: none;">
      <?php foreach ($allStudents as $stu): ?>
        <?php
        $alreadyEnrolled = false;
        foreach ($subject['enrolled'] as $enrolled) {
          if ($enrolled['id'] == $stu['id']) {
            $alreadyEnrolled = true;
            break;
          }
        }
        if (!$alreadyEnrolled):
        ?>
          <div class="dropdown-item" class="dropdown-item-styled" onclick="selectOption(<?= $index ?>, <?= e($stu['id']) ?>, '<?= e(addslashes($stu['name'] . ' (' . $stu['email'] . ')')) ?>')" data-search="<?= strtolower(e($stu['name'] . ' ' . $stu['email'])) ?>">
            <div class="dropdown-student-name"><?= e($stu['name']) ?></div>
            <div class="dropdown-student-email"><?= e($stu['email']) ?></div>
          </div>
        <?php endif; ?>
      <?php endforeach; ?>
      <div id="no-results-<?= $index ?>" class="dropdown-no-results" style="display: none;">No students found matching your search.</div>
    </div>
  </div>

  <button type="submit" class="btn-enroll-submit">+ Enroll</button>
</form>
