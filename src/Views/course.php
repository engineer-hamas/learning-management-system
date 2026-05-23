<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($course['name']) ?> - LMS Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">
    <style>
        .course-header-banner {
            background: <?= $userRole === 'teacher' ? 'linear-gradient(135deg, #10b981 0%, #059669 100%)' : 'linear-gradient(135deg, #3b82f6 0%, #2563eb 100%)' ?>;
            color: #fff;
            padding: 40px;
            border-radius: 12px;
            margin-bottom: 30px;
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
        }
        /* Modern Accordion */
        .accordion-item {
            background: #fff;
            border-radius: 12px;
            margin-bottom: 20px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
            overflow: hidden;
            transition: all 0.3s ease;
            height: fit-content;
        }
        .accordion-item:hover {
            border-color: #cbd5e1;
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
        }
        .accordion-header {
            padding: 20px 25px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #fff;
            transition: background 0.2s;
        }
        .accordion-header:hover {
            background: #f8fafc;
        }
        .accordion-header h3 {
            margin: 0;
            color: #1e293b;
            display: flex;
            align-items: center;
            font-size: 1.25rem;
        }
        .accordion-content {
            display: none;
            padding: 25px;
            border-top: 1px solid #e2e8f0;
            background: #fcfcfc;
        }
        .accordion-content.active {
            display: block;
            animation: fadeIn 0.3s ease-in-out;
        }
        .chevron {
            transition: transform 0.3s ease;
            color: #64748b;
            font-size: 1.2rem;
            display: inline-block;
        }
        .chevron.active {
            transform: rotate(180deg);
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-5px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .empty-content {
            padding: 20px;
            text-align: center;
            color: #94a3b8;
            background: #f8fafc;
            border-radius: 8px;
            border: 1px dashed #cbd5e1;
        }
        
        /* Nested Item Collapsibles */
        .item-details {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            margin-bottom: 10px;
            overflow: hidden;
            transition: box-shadow 0.2s;
        }
        .item-details:hover { box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
        .item-summary {
            padding: 15px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 500;
            background: #fcfcfc;
            transition: background 0.2s;
            list-style: none;
        }
        .item-summary::-webkit-details-marker { display: none; }
        .item-summary:hover { background: #f1f5f9; }
        .item-body {
            padding: 20px 15px;
            border-top: 1px solid #e2e8f0;
            color: #475569;
            font-size: 0.95rem;
            line-height: 1.5;
        }
        .file-download-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: #e2e8f0;
            color: #0f172a;
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            margin-top: 15px;
            transition: background 0.2s;
        }
        .file-download-btn:hover { background: #cbd5e1; }
        
        /* Premium Glassmorphic Modal */
        .modal-overlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .modal-overlay.active {
            display: flex;
            opacity: 1;
        }
        .modal-content {
            background: #fff;
            padding: 30px;
            border-radius: 16px;
            width: 100%;
            max-width: 500px;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
            transform: translateY(20px);
            transition: transform 0.3s ease;
        }
        .modal-overlay.active .modal-content {
            transform: translateY(0);
        }
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .modal-header h3 { margin: 0; color: #1e293b; }
        .modal-close {
            background: none; border: none; font-size: 1.5rem; color: #94a3b8;
            cursor: pointer; transition: color 0.2s;
        }
        .modal-close:hover { color: #0f172a; }
        
        .add-btn {
            background: #e0f2fe; color: #0369a1; border: none; padding: 6px 12px;
            border-radius: 6px; font-size: 0.85rem; font-weight: 500; cursor: pointer;
            transition: all 0.2s; display: flex; align-items: center; gap: 5px;
        }
        .add-btn:hover { background: #bae6fd; }
        
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; color: #475569; font-size: 0.9rem; font-weight: 500; }
        .form-control {
            width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px;
            font-family: inherit; font-size: 0.95rem; transition: border-color 0.2s;
        }
        .form-control:focus { outline: none; border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,0.1); }
        .submit-btn {
            width: 100%; padding: 12px; background: #3b82f6; color: #fff; border: none;
            border-radius: 8px; font-weight: 600; cursor: pointer; transition: background 0.2s;
        }
        .submit-btn:hover { background: #2563eb; }
    </style>
</head>
<body class="dashboard-body">
    <script>
        (function() {
            if (localStorage.getItem('sidebar-collapsed') === 'true') {
                document.body.classList.add('sidebar-pre-collapsed');
            }
        })();
    </script>
    <div class="dashboard-layout">
        <aside class="sidebar">
            <button type="button" id="sidebar-toggle" class="sidebar-toggle-btn" title="Toggle Sidebar">
                <span class="toggle-icon">◀</span>
            </button>
            <div class="sidebar-logo">
                <div class="logo-container">
                    <h3><span class="logo-icon">📚</span> <span class="logo-text">LMS Portal</span></h3>
                </div>
            </div>
            <nav class="sidebar-menu">
                <a href="/dashboard" class="menu-item" data-tooltip="Dashboard">
                    <span class="icon">🏠</span> <span class="menu-text">Dashboard</span>
                </a>
                <a href="/enrollments" class="menu-item active" data-tooltip="<?= e($userRole) === 'teacher' ? 'Enrollments' : 'Courses' ?>">
                    <span class="icon">🎓</span> <span class="menu-text"><?= e($userRole) === 'teacher' ? 'Enrollments' : 'Courses' ?></span>
                </a>
                <a href="/settings" class="menu-item" data-tooltip="Settings">
                    <span class="icon">⚙️</span> <span class="menu-text">Settings</span>
                </a>
            </nav>
            <div class="sidebar-footer">
                <a href="/logout" class="menu-item logout" data-tooltip="Logout">
                    <span class="icon">🚪</span> <span class="menu-text">Logout</span>
                </a>
            </div>
        </aside>

        <main class="main-content">
            <header class="dashboard-header">
                <div class="header-title">
                    <h2><a href="/enrollments" style="color: #64748b; text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color='#0f172a'" onmouseout="this.style.color='#64748b'">&larr; Back to <?= e($userRole) === 'teacher' ? 'Enrollments' : 'Courses' ?></a></h2>
                </div>
                <div class="header-profile">
                    <span class="role-badge badge-<?= e($userRole) ?>">
                        <?= ucfirst(e($userRole)) ?>
                    </span>
                    <div class="header-avatar-container">
                        <?php if (!empty($userAvatar) && file_exists(dirname(dirname(__DIR__)) . '/public' . $userAvatar)): ?>
                            <img src="<?= e($userAvatar) ?>" alt="<?= e($userName) ?>" class="header-avatar">
                        <?php else: ?>
                            <div class="header-avatar-fallback <?= e($userRole) === 'teacher' ? 'fallback-teacher' : 'fallback-student' ?>">
                                <?= strtoupper(substr(e($userName), 0, 1)) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <span class="profile-name"><?= e($userName) ?></span>
                </div>
            </header>

            <div class="dashboard-container">
                <div class="course-header-banner">
                    <h1 style="margin: 0 0 10px 0; font-size: 2.5rem;"><?= e($course['name']) ?></h1>
                    <p style="margin: 0; font-size: 1.1rem; opacity: 0.9;"><strong>Course Code:</strong> <?= e($course['code']) ?></p>
                    <?php if (!empty($course['description'])): ?>
                        <p style="margin: 15px 0 0 0; font-size: 1rem; opacity: 0.8; max-width: 800px;"><?= e($course['description']) ?></p>
                    <?php endif; ?>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
                    <!-- Course Materials -->
                    <div class="accordion-item">
                        <div class="accordion-header" onclick="toggleAccordion('mat-content', 'mat-icon')">
                            <h3>📚 Course Materials</h3>
                            <div style="display: flex; align-items: center; gap: 15px;">
                                <?php if ($userRole === 'teacher'): ?>
                                    <button class="add-btn" onclick="event.stopPropagation(); openModal('materialModal')">+ Add</button>
                                <?php endif; ?>
                                <span class="chevron active" id="mat-icon">▼</span>
                            </div>
                        </div>
                        <div class="accordion-content active" id="mat-content">
                        <?php if (empty($materials)): ?>
                            <div class="empty-content">No course materials uploaded yet.</div>
                        <?php else: ?>
                            <?php foreach ($materials as $mat): ?>
                                <details class="item-details">
                                    <summary class="item-summary">
                                        <span style="color: #1e293b; font-weight: 600;"><?= e($mat['title']) ?></span>
                                        <span style="font-size: 0.85rem; color: #64748b;"><?= date('M j, Y', strtotime($mat['created_at'])) ?></span>
                                    </summary>
                                    <div class="item-body">
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
                    <div class="accordion-item">
                        <div class="accordion-header" onclick="toggleAccordion('ass-content', 'ass-icon')">
                            <h3>📝 Assignments</h3>
                            <div style="display: flex; align-items: center; gap: 15px;">
                                <?php if ($userRole === 'teacher'): ?>
                                    <button class="add-btn" onclick="event.stopPropagation(); openModal('assignmentModal')">+ Add</button>
                                <?php endif; ?>
                                <span class="chevron active" id="ass-icon">▼</span>
                            </div>
                        </div>
                        <div class="accordion-content active" id="ass-content">
                        <?php if (empty($assignments)): ?>
                            <div class="empty-content">No assignments currently due.</div>
                        <?php else: ?>
                            <?php foreach ($assignments as $ass): ?>
                                <details class="item-details">
                                    <summary class="item-summary">
                                        <span style="color: #1e293b; font-weight: 600;"><?= e($ass['title']) ?></span>
                                        <span style="font-size: 0.85rem; color: #dc2626; font-weight: 500;">Due: <?= $ass['due_date'] ? date('M j, Y g:i A', strtotime($ass['due_date'])) : 'No due date' ?></span>
                                    </summary>
                                    <div class="item-body">
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
                    <div class="accordion-item">
                        <div class="accordion-header" onclick="toggleAccordion('exam-content', 'exam-icon')">
                            <h3>✍️ Exams</h3>
                            <div style="display: flex; align-items: center; gap: 15px;">
                                <?php if ($userRole === 'teacher'): ?>
                                    <button class="add-btn" onclick="event.stopPropagation(); openModal('examModal')">+ Add</button>
                                <?php endif; ?>
                                <span class="chevron active" id="exam-icon">▼</span>
                            </div>
                        </div>
                        <div class="accordion-content active" id="exam-content">
                        <?php if (empty($exams)): ?>
                            <div class="empty-content">No upcoming exams scheduled.</div>
                        <?php else: ?>
                            <?php foreach ($exams as $exam): ?>
                                <details class="item-details">
                                    <summary class="item-summary">
                                        <span style="color: #1e293b; font-weight: 600;"><?= e($exam['title']) ?></span>
                                        <span style="font-size: 0.85rem; color: #059669; font-weight: 500;"><?= date('M j, Y g:i A', strtotime($exam['exam_date'])) ?></span>
                                    </summary>
                                    <div class="item-body">
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
                    <div class="accordion-item">
                        <div class="accordion-header" onclick="toggleAccordion('quiz-content', 'quiz-icon')">
                            <h3>❓ Quizzes</h3>
                            <div style="display: flex; align-items: center; gap: 15px;">
                                <?php if ($userRole === 'teacher'): ?>
                                    <button class="add-btn" onclick="event.stopPropagation(); openModal('quizModal')">+ Add</button>
                                <?php endif; ?>
                                <span class="chevron active" id="quiz-icon">▼</span>
                            </div>
                        </div>
                        <div class="accordion-content active" id="quiz-content">
                        <?php if (empty($quizzes)): ?>
                            <div class="empty-content">No active quizzes.</div>
                        <?php else: ?>
                            <?php foreach ($quizzes as $quiz): ?>
                                <details class="item-details">
                                    <summary class="item-summary">
                                        <span style="color: #1e293b; font-weight: 600;"><?= e($quiz['title']) ?></span>
                                        <span style="font-size: 0.85rem; color: #64748b;"><?= date('M j, Y', strtotime($quiz['created_at'])) ?></span>
                                    </summary>
                                    <div class="item-body">
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
        <div class="modal-overlay" id="materialModal">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Upload Material</h3>
                    <button class="modal-close" onclick="closeModal('materialModal')">&times;</button>
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
        <div class="modal-overlay" id="assignmentModal">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Create Assignment</h3>
                    <button class="modal-close" onclick="closeModal('assignmentModal')">&times;</button>
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
        <div class="modal-overlay" id="examModal">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Schedule Exam</h3>
                    <button class="modal-close" onclick="closeModal('examModal')">&times;</button>
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
        <div class="modal-overlay" id="quizModal">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Add Quiz</h3>
                    <button class="modal-close" onclick="closeModal('quizModal')">&times;</button>
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
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const sidebar = document.querySelector('.sidebar');
            const toggleBtn = document.getElementById('sidebar-toggle');
            
            if (localStorage.getItem('sidebar-collapsed') === 'true') {
                sidebar.classList.add('collapsed');
            }
            document.body.classList.remove('sidebar-pre-collapsed');
            
            if (toggleBtn) {
                toggleBtn.addEventListener('click', () => {
                    sidebar.classList.toggle('collapsed');
                    localStorage.setItem('sidebar-collapsed', sidebar.classList.contains('collapsed'));
                });
            }
        });

        function toggleAccordion(contentId, iconId) {
            const content = document.getElementById(contentId);
            const icon = document.getElementById(iconId);
            
            if (content.classList.contains('active')) {
                content.classList.remove('active');
                icon.classList.remove('active');
            } else {
                content.classList.add('active');
                icon.classList.add('active');
            }
        }
    </script>
</body>
</html>
