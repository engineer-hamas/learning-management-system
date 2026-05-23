<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enrollments - LMS Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">
    <style>
        /* Modern Accordion for Teachers */
        .accordion-item {
            background: #fff;
            border-radius: 12px;
            margin-bottom: 20px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
            overflow: hidden;
            transition: all 0.3s ease;
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
        
        /* Premium Glassmorphic Cards for Students */
        .student-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            border: 1px solid rgba(226, 232, 240, 0.8);
            padding: 25px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            height: 100%;
        }
        .student-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 4px;
            background: linear-gradient(90deg, #3b82f6, #8b5cf6);
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .student-card:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            border-color: #cbd5e1;
        }
        .student-card:hover::before {
            opacity: 1;
        }
        .enter-course-btn {
            display: inline-block;
            margin-top: 25px;
            padding: 12px 0;
            text-align: center;
            background: #eff6ff;
            color: #2563eb;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
            width: 100%;
        }
        .student-card:hover .enter-course-btn {
            background: #2563eb;
            color: #fff;
        }
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
                    <h2>Course Enrollments</h2>
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
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger" style="margin-bottom: 20px; padding: 15px; border-radius: 8px; background: #fee2e2; color: #991b1b; border: 1px solid #fecaca;">
                        <?= e($error) ?>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($success)): ?>
                    <div class="alert alert-success" style="margin-bottom: 20px; padding: 15px; border-radius: 8px; background: #dcfce7; color: #166534; border: 1px solid #bbf7d0;">
                        <?= e($success) ?>
                    </div>
                <?php endif; ?>

                <?php if ($userRole === 'teacher'): ?>
                    <!-- Teacher View -->
                    <div class="welcome-card" style="margin-bottom: 30px;">
                        <h1>Manage Enrollments</h1>
                        <p>Below are the subjects you teach. You can enroll or remove students from your courses.</p>
                    </div>

                    <?php if (empty($teacherSubjects)): ?>
                        <div class="empty-state" style="text-align: center; padding: 40px; background: #fff; border-radius: 12px; border: 1px solid #e2e8f0;">
                            <p>You are not currently assigned to teach any subjects.</p>
                            <p style="font-size: 0.9rem; color: #64748b;">Update your subjects in your Professional Settings.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($teacherSubjects as $index => $subject): ?>
                            <div class="accordion-item">
                                <div class="accordion-header" onclick="toggleAccordion('acc-<?= $index ?>', 'icon-<?= $index ?>')">
                                    <div style="display: flex; align-items: center; gap: 15px;">
                                        <div style="background: #e0f2fe; color: #0369a1; width: 45px; height: 45px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                                            📚
                                        </div>
                                        <div>
                                            <h3 style="margin: 0 0 4px 0; font-size: 1.25rem; color: #1e293b; line-height: 1.2;">
                                                <?= e($subject['name']) ?>
                                            </h3>
                                            <span style="font-size: 0.85rem; color: #64748b; font-weight: 500;">CODE: <?= e($subject['code']) ?> &nbsp;&bull;&nbsp; <?= count($subject['enrolled']) ?> Student(s)</span>
                                        </div>
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 20px;">
                                        <a href="/course?id=<?= e($subject['id']) ?>" style="padding: 8px 16px; background: #10b981; color: #fff; border-radius: 6px; text-decoration: none; font-size: 0.9rem; font-weight: 600; transition: all 0.2s; box-shadow: 0 2px 4px rgba(16,185,129,0.2);" onclick="event.stopPropagation();" onmouseover="this.style.background='#059669'; this.style.transform='translateY(-1px)';" onmouseout="this.style.background='#10b981'; this.style.transform='none';">Manage Course &rarr;</a>
                                        <span class="chevron" id="icon-<?= $index ?>">▼</span>
                                    </div>
                                </div>
                                
                                <div class="accordion-content" id="acc-<?= $index ?>">
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; padding-bottom: 15px; border-bottom: 1px solid #e2e8f0;">
                                        <h4 style="margin: 0; color: #334155; font-size: 1.1rem;">Manage Enrollments</h4>
                                        <!-- Enroll Student Form with Searchable Dropdown -->
                                        <form action="/enrollments/add" method="POST" style="display: flex; gap: 10px; align-items: center; overflow: visible;">
                                            <input type="hidden" name="subject_id" value="<?= e($subject['id']) ?>">
                                            
                                            <div class="searchable-dropdown" style="position: relative;">
                                                <input type="text" id="search-input-<?= $index ?>" placeholder="Search student name or email..." style="padding: 9px 12px; border-radius: 6px; border: 1px solid #cbd5e1; font-family: inherit; font-size: 0.9rem; width: 280px; outline: none; transition: border-color 0.2s;" onfocus="openDropdown(<?= $index ?>)" oninput="filterDropdown(<?= $index ?>)" autocomplete="off">
                                                <input type="hidden" name="student_id" id="hidden-input-<?= $index ?>" required>
                                                
                                                <div id="dropdown-list-<?= $index ?>" style="display: none; position: absolute; top: 100%; left: 0; right: 0; background: #fff; border: 1px solid #cbd5e1; border-radius: 6px; margin-top: 4px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); max-height: 250px; overflow-y: auto; z-index: 50;">
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
                                                            <div class="dropdown-item" style="padding: 10px 12px; cursor: pointer; border-bottom: 1px solid #f1f5f9; transition: background 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''" onclick="selectOption(<?= $index ?>, <?= e($stu['id']) ?>, '<?= e(addslashes($stu['name'] . ' (' . $stu['email'] . ')')) ?>')" data-search="<?= strtolower(e($stu['name'] . ' ' . $stu['email'])) ?>">
                                                                <div style="font-weight: 500; color: #1e293b; font-size: 0.9rem;"><?= e($stu['name']) ?></div>
                                                                <div style="font-size: 0.8rem; color: #64748b;"><?= e($stu['email']) ?></div>
                                                            </div>
                                                        <?php endif; ?>
                                                    <?php endforeach; ?>
                                                    <div id="no-results-<?= $index ?>" style="display: none; padding: 12px; text-align: center; color: #94a3b8; font-size: 0.85rem;">No students found matching your search.</div>
                                                </div>
                                            </div>
                                            
                                            <button type="submit" style="padding: 9px 18px; border-radius: 6px; border: none; background: #3b82f6; color: #fff; cursor: pointer; font-weight: 600; transition: background 0.2s;" onmouseover="this.style.background='#2563eb'" onmouseout="this.style.background='#3b82f6'">+ Enroll</button>
                                        </form>
                                    </div>

                                    <!-- Enrolled Students List -->
                                    <?php if (empty($subject['enrolled'])): ?>
                                        <div style="text-align: center; padding: 30px; background: #fff; border: 1px dashed #cbd5e1; border-radius: 8px; color: #94a3b8;">
                                            <div style="font-size: 2rem; margin-bottom: 10px;">👥</div>
                                            No students are currently enrolled in this subject.
                                        </div>
                                    <?php else: ?>
                                        <div style="overflow-x: auto; border: 1px solid #e2e8f0; border-radius: 8px; box-shadow: inset 0 2px 4px 0 rgba(0,0,0,0.02);">
                                            <table style="width: 100%; border-collapse: collapse;">
                                                <thead>
                                                    <tr style="border-bottom: 1px solid #e2e8f0; background: #f8fafc;">
                                                        <th style="text-align: left; padding: 14px 20px; font-weight: 600; color: #475569; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">Student Name</th>
                                                        <th style="text-align: left; padding: 14px 20px; font-weight: 600; color: #475569; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">Email Address</th>
                                                        <th style="text-align: left; padding: 14px 20px; font-weight: 600; color: #475569; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">Enrolled On</th>
                                                        <th style="text-align: right; padding: 14px 20px; font-weight: 600; color: #475569; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($subject['enrolled'] as $stu): ?>
                                                        <tr style="border-bottom: 1px solid #f1f5f9; background: #fff; transition: background 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='#fff'">
                                                            <td style="padding: 14px 20px; font-weight: 500; color: #1e293b;">
                                                                <div style="display: flex; align-items: center; gap: 12px;">
                                                                    <div style="width: 36px; height: 36px; border-radius: 50%; background: #e2e8f0; display: flex; align-items: center; justify-content: center; color: #475569; font-size: 0.85rem; font-weight: bold;">
                                                                        <?= strtoupper(substr(e($stu['name']), 0, 1)) ?>
                                                                    </div>
                                                                    <?= e($stu['name']) ?>
                                                                </div>
                                                            </td>
                                                            <td style="padding: 14px 20px; color: #64748b; font-size: 0.95rem;"><?= e($stu['email']) ?></td>
                                                            <td style="padding: 14px 20px; color: #64748b; font-size: 0.95rem;"><?= date('M j, Y \a\t g:i A', strtotime($stu['enrolled_at'])) ?></td>
                                                            <td style="padding: 14px 20px; text-align: right;">
                                                                <form action="/enrollments/remove" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to unenroll this student from the course?');">
                                                                    <input type="hidden" name="subject_id" value="<?= e($subject['id']) ?>">
                                                                    <input type="hidden" name="student_id" value="<?= e($stu['id']) ?>">
                                                                    <button type="submit" style="padding: 8px 14px; border-radius: 6px; border: 1px solid #fecaca; background: #fef2f2; color: #dc2626; cursor: pointer; font-size: 0.85rem; font-weight: 500; transition: all 0.2s;" onmouseover="this.style.background='#fee2e2'" onmouseout="this.style.background='#fef2f2'">Remove</button>
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
                        <?php endforeach; ?>
                    <?php endif; ?>

                <?php else: ?>
                    <!-- Student View -->
                    <div class="welcome-card" style="margin-bottom: 30px;">
                        <h1>My Enrollments</h1>
                        <p>Below are the subjects you are currently enrolled in. If you need to drop a course, please contact your instructor.</p>
                    </div>

                    <?php if (empty($studentEnrollments)): ?>
                        <div class="empty-state" style="text-align: center; padding: 40px; background: #fff; border-radius: 12px; border: 1px solid #e2e8f0;">
                            <p>You are not currently enrolled in any subjects.</p>
                        </div>
                    <?php else: ?>
                        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
                            <?php foreach ($studentEnrollments as $sub): ?>
                                <a href="/course?id=<?= e($sub['id']) ?>" style="text-decoration: none; color: inherit; display: block;">
                                    <div class="student-card">
                                        <div>
                                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
                                                <div style="background: #e0f2fe; color: #2563eb; width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.6rem; box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.1);">
                                                    📘
                                                </div>
                                                <span style="background: #f1f5f9; color: #475569; padding: 6px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; letter-spacing: 0.5px; border: 1px solid #e2e8f0;"><?= e($sub['code']) ?></span>
                                            </div>
                                            <h3 style="margin: 0 0 8px 0; color: #1e293b; font-size: 1.3rem; font-weight: 700; line-height: 1.3;"><?= e($sub['name']) ?></h3>
                                            <p style="margin: 0; color: #64748b; font-size: 0.9rem; display: flex; align-items: center; gap: 6px; font-weight: 500;">
                                                <span style="font-size: 1.1rem; opacity: 0.8;">🗓️</span> Enrolled <?= date('M j, Y', strtotime($sub['enrolled_at'])) ?>
                                            </p>
                                        </div>
                                        <div class="enter-course-btn">
                                            Enter Course &rarr;
                                        </div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </main>
    </div>
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

        // Accordion functionality for Teacher View
        function toggleAccordion(contentId, iconId) {
            const content = document.getElementById(contentId);
            const icon = document.getElementById(iconId);
            
            if (content.classList.contains('active')) {
                // Collapse
                content.classList.remove('active');
                icon.classList.remove('active');
            } else {
                // Auto-collapse others (Optional, makes for a cleaner UI)
                document.querySelectorAll('.accordion-content').forEach(el => el.classList.remove('active'));
                document.querySelectorAll('.chevron').forEach(el => el.classList.remove('active'));
                
                // Expand current
                content.classList.add('active');
                icon.classList.add('active');
                
                // Ensure parent has visible overflow when opened so dropdowns aren't clipped
                content.style.overflow = "visible"; 
            }
        }
        
        // Searchable Dropdown Logic
        function openDropdown(index) {
            document.getElementById('dropdown-list-' + index).style.display = 'block';
            document.getElementById('search-input-' + index).style.borderColor = '#3b82f6';
        }
        
        function filterDropdown(index) {
            const input = document.getElementById('search-input-' + index);
            const filter = input.value.toLowerCase();
            const list = document.getElementById('dropdown-list-' + index);
            const items = list.querySelectorAll('.dropdown-item');
            let hasVisible = false;
            
            items.forEach(item => {
                const text = item.getAttribute('data-search');
                if (text.includes(filter)) {
                    item.style.display = 'block';
                    hasVisible = true;
                } else {
                    item.style.display = 'none';
                }
            });
            
            document.getElementById('no-results-' + index).style.display = hasVisible ? 'none' : 'block';
        }
        
        function selectOption(index, id, label) {
            document.getElementById('search-input-' + index).value = label;
            document.getElementById('hidden-input-' + index).value = id;
            document.getElementById('dropdown-list-' + index).style.display = 'none';
            document.getElementById('search-input-' + index).style.borderColor = '#cbd5e1';
        }
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            document.querySelectorAll('.searchable-dropdown').forEach((dropdown, idx) => {
                if (!dropdown.contains(e.target)) {
                    const list = dropdown.querySelector('[id^="dropdown-list-"]');
                    const input = dropdown.querySelector('[id^="search-input-"]');
                    if (list) list.style.display = 'none';
                    if (input) input.style.borderColor = '#cbd5e1';
                }
            });
        });
    </script>
</body>
</html>
