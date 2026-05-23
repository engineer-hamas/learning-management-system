<?php ob_start(); ?>
<h2><a href="/enrollments" style="color: #64748b; text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color='#0f172a'" onmouseout="this.style.color='#64748b'">&larr; Back to <?= e($userRole) === 'teacher' ? 'Enrollments' : 'Courses' ?></a></h2>
<?php $headerTitle = ob_get_clean(); ?>
<?php require __DIR__ . '/components/common/page-header.php'; ?>

<div class="dashboard-container">
  <?php
  $bannerExtraClass = '';
  $bannerTitleClass = '';
  $bannerTitleStyle = 'margin: 0 0 10px 0; font-size: 2.5rem;';
  $bannerTitle = e($course['name']);
  $bannerSubtitleClass = '';
  $bannerSubtitleStyle = 'margin: 0; font-size: 1.1rem; opacity: 0.9;';
  $bannerSubtitle = '<strong>Course Code:</strong> ' . e($course['code']);
  $hideDescription = false;
  require __DIR__ . '/components/common/course-banner.php';
  ?>

  <?php if ($userRole === 'teacher'): ?>
    <!-- Manage Enrollments (Full Row at Top) -->
    <div style="margin-bottom: 30px;">
      <?php require __DIR__ . '/components/course/enrollments-accordion.php'; ?>
    </div>
  <?php endif; ?>

  <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
    <!-- Course Materials -->
    <div class="accordion__item">
      <div class="accordion__header" onclick="toggleAccordion('mat-content', 'mat-icon')">
        <h3>📚 Course Materials</h3>
        <div style="display: flex; align-items: center; gap: 15px;">
          <?php if ($userRole === 'teacher'): ?>
            <button class="add-btn" onclick="event.stopPropagation(); openModal('materialModal')">+ Add</button>
          <?php endif; ?>
          <span class="accordion__icon" id="mat-icon">▼</span>
        </div>
      </div>
      <div class="accordion__content" id="mat-content">
        <?php if (empty($materials)): ?>
          <div class="empty-state">No course materials uploaded yet.</div>
        <?php else: ?>
          <?php foreach ($materials as $mat): ?>
            <details class="accordion-detail">
              <summary class="accordion-detail__summary">
                <span style="color: #1e293b; font-weight: 600;"><?= e($mat['title']) ?></span>
                <span style="font-size: 0.85rem; color: #64748b;"><?= date('M j, Y', strtotime($mat['created_at'])) ?></span>
              </summary>
              <div class="accordion-detail__body">
                <?php if (!empty($mat['file_path'])): ?>
                  <a href="<?= e($mat['file_path']) ?>" download="<?= e($mat['original_filename']) ?>" class="file-download-btn">⬇️ Download <?= e($mat['original_filename']) ?></a>
                <?php endif; ?>

                <?php if ($userRole === 'teacher'): ?>
                  <form action="/course/material/delete" method="POST" style="margin-top: 15px;" onsubmit="return confirm('Delete this material?');">
                    <input type="hidden" name="id" value="<?= e($mat['id']) ?>">
                    <input type="hidden" name="subject_id" value="<?= e($course['id']) ?>">
                    <button type="submit" style="background: none; border: none; color: #ef4444; cursor: pointer; padding: 0; font-size: 0.85rem; font-weight: 500; text-decoration: underline;">Delete Material</button>
                  </form>
                <?php endif; ?>
              </div>
            </details>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>

    <!-- Assignments -->
    <div class="accordion__item">
      <div class="accordion__header" onclick="toggleAccordion('ass-content', 'ass-icon')">
        <h3>📝 Assignments</h3>
        <div style="display: flex; align-items: center; gap: 15px;">
          <?php if ($userRole === 'teacher'): ?>
            <button class="add-btn" onclick="event.stopPropagation(); openModal('assignmentModal')">+ Add</button>
          <?php endif; ?>
          <span class="accordion__icon" id="ass-icon">▼</span>
        </div>
      </div>
      <div class="accordion__content" id="ass-content">
        <?php if (empty($assignments)): ?>
          <div class="empty-state">No assignments currently due.</div>
        <?php else: ?>
          <?php foreach ($assignments as $ass): ?>
            <details class="accordion-detail">
              <summary class="accordion-detail__summary">
                <span style="color: #1e293b; font-weight: 600;"><?= e($ass['title']) ?></span>
                <span style="font-size: 0.85rem; color: #dc2626; font-weight: 500;">Due: <?= $ass['due_date'] ? date('M j, Y g:i A', strtotime($ass['due_date'])) : 'No due date' ?></span>
              </summary>
              <div class="accordion-detail__body">
                <p style="margin-top: 0;"><?= e($ass['description']) ?></p>

                <?php if (!empty($ass['file_path'])): ?>
                  <a href="<?= e($ass['file_path']) ?>" download="<?= e($ass['original_filename']) ?>" class="file-download-btn">⬇️ Download <?= e($ass['original_filename']) ?></a>
                <?php endif; ?>

                <?php if ($userRole === 'teacher'): ?>
                  <form action="/course/assignment/delete" method="POST" style="margin-top: 15px;" onsubmit="return confirm('Delete this assignment?');">
                    <input type="hidden" name="id" value="<?= e($ass['id']) ?>">
                    <input type="hidden" name="subject_id" value="<?= e($course['id']) ?>">
                    <button type="submit" style="background: none; border: none; color: #ef4444; cursor: pointer; padding: 0; font-size: 0.85rem; font-weight: 500; text-decoration: underline;">Delete Assignment</button>
                  </form>
                <?php endif; ?>
              </div>
            </details>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>

    <!-- Exams -->
    <div class="accordion__item">
      <div class="accordion__header" onclick="toggleAccordion('exam-content', 'exam-icon')">
        <h3>✍️ Exams</h3>
        <div style="display: flex; align-items: center; gap: 15px;">
          <?php if ($userRole === 'teacher'): ?>
            <button class="add-btn" onclick="event.stopPropagation(); openModal('examModal')">+ Add</button>
          <?php endif; ?>
          <span class="accordion__icon" id="exam-icon">▼</span>
        </div>
      </div>
      <div class="accordion__content" id="exam-content">
        <?php if (empty($exams)): ?>
          <div class="empty-state">No upcoming exams scheduled.</div>
        <?php else: ?>
          <?php foreach ($exams as $exam): ?>
            <details class="accordion-detail">
              <summary class="accordion-detail__summary">
                <span style="color: #1e293b; font-weight: 600;"><?= e($exam['title']) ?></span>
                <span style="font-size: 0.85rem; color: #059669; font-weight: 500;"><?= date('M j, Y g:i A', strtotime($exam['exam_date'])) ?></span>
              </summary>
              <div class="accordion-detail__body">
                <p style="margin-top: 0;"><strong>Duration:</strong> <?= (int)$exam['duration_minutes'] ?> minutes</p>

                <?php if (!empty($exam['file_path'])): ?>
                  <a href="<?= e($exam['file_path']) ?>" download="<?= e($exam['original_filename']) ?>" class="file-download-btn">⬇️ Download <?= e($exam['original_filename']) ?></a>
                <?php endif; ?>

                <?php if ($userRole === 'teacher'): ?>
                  <form action="/course/exam/delete" method="POST" style="margin-top: 15px;" onsubmit="return confirm('Delete this exam?');">
                    <input type="hidden" name="id" value="<?= e($exam['id']) ?>">
                    <input type="hidden" name="subject_id" value="<?= e($course['id']) ?>">
                    <button type="submit" style="background: none; border: none; color: #ef4444; cursor: pointer; padding: 0; font-size: 0.85rem; font-weight: 500; text-decoration: underline;">Delete Exam</button>
                  </form>
                <?php endif; ?>
              </div>
            </details>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>

    <!-- Quizzes -->
    <div class="accordion__item">
      <div class="accordion__header" onclick="toggleAccordion('quiz-content', 'quiz-icon')">
        <h3>❓ Quizzes</h3>
        <div style="display: flex; align-items: center; gap: 15px;">
          <?php if ($userRole === 'teacher'): ?>
            <button class="add-btn" onclick="event.stopPropagation(); openModal('quizModal')">+ Add</button>
          <?php endif; ?>
          <span class="accordion__icon" id="quiz-icon">▼</span>
        </div>
      </div>
      <div class="accordion__content" id="quiz-content">
        <?php if (empty($quizzes)): ?>
          <div class="empty-state">No active quizzes.</div>
        <?php else: ?>
          <?php foreach ($quizzes as $quiz): ?>
            <details class="accordion-detail">
              <summary class="accordion-detail__summary">
                <span style="color: #1e293b; font-weight: 600;"><?= e($quiz['title']) ?></span>
                <span style="font-size: 0.85rem; color: #64748b;"><?= date('M j, Y', strtotime($quiz['created_at'])) ?></span>
              </summary>
              <div class="accordion-detail__body">
                <p style="margin-top: 0;"><?= nl2br(e($quiz['description'])) ?></p>

                <?php if (!empty($quiz['file_path'])): ?>
                  <a href="<?= e($quiz['file_path']) ?>" download="<?= e($quiz['original_filename']) ?>" class="file-download-btn">⬇️ Download <?= e($quiz['original_filename']) ?></a>
                <?php endif; ?>

                <?php if ($userRole === 'teacher'): ?>
                  <form action="/course/quiz/delete" method="POST" style="margin-top: 15px;" onsubmit="return confirm('Delete this quiz?');">
                    <input type="hidden" name="id" value="<?= e($quiz['id']) ?>">
                    <input type="hidden" name="subject_id" value="<?= e($course['id']) ?>">
                    <button type="submit" style="background: none; border: none; color: #ef4444; cursor: pointer; padding: 0; font-size: 0.85rem; font-weight: 500; text-decoration: underline;">Delete Quiz</button>
                  </form>
                <?php endif; ?>
              </div>
            </details>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
</main>
</div>

<!-- Modals for Teachers -->
<?php if ($userRole === 'teacher'): ?>
  <!-- Material Modal -->
  <div class="modal" id="materialModal">
    <div class="modal__content">
      <div class="modal__header">
        <h3>Upload Material</h3>
        <button class="modal__close" onclick="closeModal('materialModal')">&times;</button>
      </div>
      <form action="/course/material" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="subject_id" value="<?= e($course['id']) ?>">
        <div class="form-group">
          <label>Title</label>
          <input type="text" name="title" class="form-control" required placeholder="e.g. Lecture 1 Slides">
        </div>
        <div class="form-group">
          <label>File (Required)</label>
          <input type="file" name="content_file" class="form-control" required>
        </div>
        <div class="form-group">
          <label>Save File As (Optional)</label>
          <input type="text" name="saved_filename" class="form-control" placeholder="e.g. Lecture_1.pdf">
        </div>
        <button type="submit" class="submit-btn">Upload</button>
      </form>
    </div>
  </div>

  <!-- Assignment Modal -->
  <div class="modal" id="assignmentModal">
    <div class="modal__content">
      <div class="modal__header">
        <h3>Create Assignment</h3>
        <button class="modal__close" onclick="closeModal('assignmentModal')">&times;</button>
      </div>
      <form action="/course/assignment" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="subject_id" value="<?= e($course['id']) ?>">
        <div class="form-group">
          <label>Title</label>
          <input type="text" name="title" class="form-control" required>
        </div>
        <div class="form-group">
          <label>Description</label>
          <textarea name="description" class="form-control" rows="3"></textarea>
        </div>
        <div class="form-group">
          <label>Due Date</label>
          <input type="datetime-local" name="due_date" class="form-control" required>
        </div>
        <div class="form-group">
          <label>Attach File (Optional)</label>
          <input type="file" name="content_file" class="form-control">
        </div>
        <div class="form-group">
          <label>Save Attached File As (Optional)</label>
          <input type="text" name="saved_filename" class="form-control" placeholder="e.g. Assignment_Details.pdf">
        </div>
        <button type="submit" class="submit-btn">Create</button>
      </form>
    </div>
  </div>

  <!-- Exam Modal -->
  <div class="modal" id="examModal">
    <div class="modal__content">
      <div class="modal__header">
        <h3>Schedule Exam</h3>
        <button class="modal__close" onclick="closeModal('examModal')">&times;</button>
      </div>
      <form action="/course/exam" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="subject_id" value="<?= e($course['id']) ?>">
        <div class="form-group">
          <label>Title</label>
          <input type="text" name="title" class="form-control" required>
        </div>
        <div class="form-group">
          <label>Date & Time</label>
          <input type="datetime-local" name="exam_date" class="form-control" required>
        </div>
        <div class="form-group">
          <label>Duration (Minutes)</label>
          <input type="number" name="duration_minutes" class="form-control" value="60" required>
        </div>
        <div class="form-group">
          <label>Attach File (Optional)</label>
          <input type="file" name="content_file" class="form-control">
        </div>
        <div class="form-group">
          <label>Save Attached File As (Optional)</label>
          <input type="text" name="saved_filename" class="form-control" placeholder="e.g. Exam_Syllabus.pdf">
        </div>
        <button type="submit" class="submit-btn">Schedule</button>
      </form>
    </div>
  </div>

  <!-- Quiz Modal -->
  <div class="modal" id="quizModal">
    <div class="modal__content">
      <div class="modal__header">
        <h3>Add Quiz</h3>
        <button class="modal__close" onclick="closeModal('quizModal')">&times;</button>
      </div>
      <form action="/course/quiz" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="subject_id" value="<?= e($course['id']) ?>">
        <div class="form-group">
          <label>Title</label>
          <input type="text" name="title" class="form-control" required>
        </div>
        <div class="form-group">
          <label>Description / Link</label>
          <textarea name="description" class="form-control" rows="3"></textarea>
        </div>
        <div class="form-group">
          <label>Attach File (Optional)</label>
          <input type="file" name="content_file" class="form-control">
        </div>
        <div class="form-group">
          <label>Save Attached File As (Optional)</label>
          <input type="text" name="saved_filename" class="form-control" placeholder="e.g. Quiz_Instructions.pdf">
        </div>
        <button type="submit" class="submit-btn">Add Quiz</button>
      </form>
    </div>
  </div>

  <script>
    function openModal(id) {
      document.getElementById(id).classList.add('active');
    }

    function closeModal(id) {
      document.getElementById(id).classList.remove('active');
    }
    // Close on overlay click
    document.querySelectorAll('.modal-overlay').forEach(overlay => {
      overlay.addEventListener('click', (e) => {
        if (e.target === overlay) closeModal(overlay.id);
      });
    });
  </script>
<?php endif; ?>
